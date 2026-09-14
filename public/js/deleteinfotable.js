
// เมื่อกดปุ่มลบ ให้แสดง modal และส่งค่า data-url และ data-name ไปยัง modal
$(document).ready(function() {
    $('#modalDel').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // ปุ่มที่ถูกคลิก
        var url = button.data('url');        // ดึงค่า data-url
        var name = button.data('name');      // ดึงค่า data-name

        // หยอดชื่อและ URL ลงใน Modal อัตโนมัติ
        $('#del_name').text(name ? `"${name}"` : '');
        $('#btnConfirmDelete').attr('href', url);
    });

});

