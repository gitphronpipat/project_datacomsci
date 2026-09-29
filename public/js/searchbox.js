/**
 * ==============================================================================
 * สคริปต์กล่องค้นหาตารางและไฮไลต์คำค้นหา (Universal Table Search & Highlighter)
 * ตำแหน่งไฟล์: public/js/searchbox.js
 * ==============================================================================
 * คำอธิบาย:
 * โมดูลค้นหา กรองแถวในตาราง และไฮไลต์คำค้นหาแบบ Real-Time ฉบับปรับปรุงกระชับ (Clean & Fast)
 * ตัดโค้ดขยะและฟังก์ชันที่ไม่ได้ใช้งานออก เพื่อความเร็วสูงสุดและไม่กินหน่วยความจำเครื่อง
 * ==============================================================================
 */

(function (window, $) {
    'use strict';

    if (typeof $ === 'undefined') {
        console.warn('[SearchBox] จำเป็นต้องโหลด jQuery ก่อนใช้งาน searchbox.js');
        return;
    }

    /**
     * ไฮไลต์คำค้นหาเฉพาะข้อความจริง (Text Node) อย่างปลอดภัย ไม่กระทบปุ่มกดหรือแท็ก HTML
     * @param {HTMLElement} element - โหนดแม่ใน DOM (เช่น ตาราง หรือ แถว)
     * @param {string[]} terms - รายการคำค้นหาที่ต้องการไฮไลต์
     */
    function highlightInNode(element, terms) {
        if (!element || !terms || terms.length === 0) return;

        // Escape อักขระพิเศษสำหรับ RegExp ป้องกันข้อผิดพลาดทางไวยากรณ์
        const escapedTerms = terms.map(function (t) {
            return t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        });
        const regex = new RegExp('(' + escapedTerms.join('|') + ')', 'gi');

        // สำรวจเฉพาะ Text Node โดยข้ามปุ่มกดและคลาสที่ไม่ต้องการค้นหา
        const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT, {
            acceptNode: function (node) {
                if (node.parentElement && node.parentElement.closest('button, .btn, script, style, .no-highlight, .no-search')) {
                    return NodeFilter.FILTER_REJECT;
                }
                return node.nodeValue && node.nodeValue.trim().length > 0
                    ? NodeFilter.FILTER_ACCEPT
                    : NodeFilter.FILTER_REJECT;
            }
        });

        const textNodes = [];
        while (walker.nextNode()) {
            if (regex.test(walker.currentNode.nodeValue)) {
                textNodes.push(walker.currentNode);
            }
            regex.lastIndex = 0;
        }

        textNodes.forEach(function (node) {
            const parent = node.parentNode;
            if (!parent) return;

            const val = node.nodeValue;
            const frag = document.createDocumentFragment();
            let lastIdx = 0;

            val.replace(regex, function (match, p1, offset) {
                if (offset > lastIdx) {
                    frag.appendChild(document.createTextNode(val.substring(lastIdx, offset)));
                }
                const mark = document.createElement('mark');
                mark.className = 'search-highlight';
                mark.style.backgroundColor = '#fff39bff';
                mark.style.color = '#854d0e';
                mark.style.padding = '0.08em 0.25em';
                mark.style.borderRadius = '3px';
                mark.style.fontWeight = '600';
                mark.textContent = match;
                frag.appendChild(mark);
                lastIdx = offset + match.length;
                return match;
            });

            if (lastIdx < val.length) {
                frag.appendChild(document.createTextNode(val.substring(lastIdx)));
            }
            parent.replaceChild(frag, node);
        });
    }

    /**
     * ล้างแท็ก <mark class="search-highlight"> ออกทั้งหมด คืนสภาพข้อความปกติ
     * @param {HTMLElement} element - โหนดแม่ใน DOM
     */
    function removeHighlight(element) {
        if (!element) return;
        const marks = element.querySelectorAll('mark.search-highlight');
        for (let i = 0; i < marks.length; i++) {
            const mark = marks[i];
            const parent = mark.parentNode;
            if (parent) {
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize(); // รวม Text Node ที่ติดกันกลับเป็นก้อนเดียว
            }
        }
    }

    /**
     * กรองแถวในตารางตามคำค้นหา พร้อมจัดการไฮไลต์คำและแถวแจ้งเตือน
     * @param {string|jQuery} tableSelector - ตัวเลือกตารางเป้าหมาย
     * @param {string} query - คำค้นหา
     * @param {Object} [options] - ตัวเลือกเสริม เช่น highlightToggle, noResultText
     * @returns {number} จำนวนแถวที่ค้นพบ
     */
    function filterTableRows(tableSelector, query, options) {
        options = $.extend({
            noResultText: 'ไม่พบข้อมูลที่ตรงกับคำค้นหา',
            noResultSubText: 'ลองตรวจสอบตัวสะกด หรือกดปุ่มล้างคำค้นหาเพื่อดูข้อมูลทั้งหมด',
            ignoreSelector: '.no-search, .row-empty-placeholder',
            highlightToggle: null
        }, options || {});

        const $table = $(tableSelector);
        if ($table.length === 0) return 0;

        const $tbody = $table.find('tbody');
        if ($tbody.length === 0) return 0;

        // 1. ล้างไฮไลต์เก่าทั้งหมดในตารางออกก่อนเสมอ เพื่อให้คำนวณข้อความได้ถูกต้อง 100%
        removeHighlight($table[0]);

        // 2. ลบแถวแจ้งเตือนผลการค้นหาเดิมออก
        $tbody.find('.row-search-not-found').remove();

        const $rows = $tbody.find('tr').not(options.ignoreSelector).not('.row-search-not-found');
        if ($rows.length === 0) return 0;

        const cleanQuery = (query || '').toLowerCase().trim();
        // แยกคำค้นหาด้วยช่องว่าง เพื่อให้ค้นหาหลายคำพร้อมกันได้ (AND logic เช่น "สมชาย 66")
        const terms = cleanQuery ? cleanQuery.split(/\s+/).filter(Boolean) : [];

        let visibleCount = 0;

        // 3. วนลูปกรองแถวข้อมูล
        $rows.each(function () {
            const $row = $(this);
            const rowText = $row.text().toLowerCase();

            let isMatch = true;
            if (terms.length > 0) {
                for (let i = 0; i < terms.length; i++) {
                    if (rowText.indexOf(terms[i]) === -1) {
                        isMatch = false;
                        break;
                    }
                }
            }

            if (isMatch) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });

        // 4. จัดการไฮไลต์คำค้นหา (ถ้าผู้ใช้ติ๊ก Checkbox เปิดไว้ หรือส่ง highlight: true มา)
        const isHighlightChecked = options.highlightToggle 
            ? $(options.highlightToggle).is(':checked') 
            : (options.highlight === true);
        if (isHighlightChecked && terms.length > 0) {
            $rows.each(function () {
                if ($(this).is(':visible')) {
                    highlightInNode(this, terms);
                }
            });
        }

        // 5. แสดงข้อความแจ้งเตือนเมื่อค้นหาแล้วไม่พบข้อมูล (สไตล์ DataTables zeroRecords)
        if (visibleCount === 0 && cleanQuery !== '') {
            const colCount = $table.find('thead th').length || $table.find('tr:first td').length || 8;
            const safeQueryHtml = $('<div>').text(cleanQuery).html();
            const notFoundRow = '' +
                '<tr class="row-search-not-found">' +
                    '<td colspan="' + colCount + '" class="text-center py-5 text-muted">' +
                        '<div class="mb-2"><i class="fas fa-search fa-2x text-secondary opacity-50"></i></div>' +
                        '<div class="fw-semibold text-dark fs-6">' + options.noResultText + ' "' + safeQueryHtml + '"</div>' +
                        '<div class="small text-muted mt-1">' + options.noResultSubText + '</div>' +
                    '</td>' +
                '</tr>';
            $tbody.append(notFoundRow);
        }

        return visibleCount;
    }

    // ส่งออกเป็น Global Object (window.SearchBox)
    window.SearchBox = {
        filterTable: filterTableRows,
        highlight: highlightInNode,
        removeHighlight: removeHighlight
    };

})(window, window.jQuery);
