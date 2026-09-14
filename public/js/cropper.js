
    // =========================================================
    //  ระบบอัปโหลดและตัดรูปโปรไฟล์ (Dropzone + Cropper 2-in-1)
    // =========================================================
    let cropper = null;
    let currentInputFile = null;

    // เคลียร์ค่า input file เพื่อให้สามารถเลือกรูปเดิมซ้ำได้เสมอ (เช่น เมื่อผู้ใช้กดผิดแล้วต้องการเลือกรูปเดิมอีกครั้ง)
    function clearFileInput() {
        const fileInput = document.getElementById('profile_picture');
        if (fileInput) {
            fileInput.value = '';
        }
    }

    // ฟังก์ชันรีเซ็ตกลับไปหน้าต่าง Dropzone (เลือก/ลากวางรูป)
    function resetToDropzone() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        $('#dropzoneArea').show();
        $('#cropperArea').hide();
        $('#btnCropApply').hide();
        const cropImageEl = document.getElementById('cropImageSource');
        if (cropImageEl) {
            cropImageEl.src = '';
            cropImageEl.removeAttribute('src');
        }
    }

    // ฟังก์ชันเมื่อได้รับรูปภาพ (จากไฟล์ในเครื่อง, ลากวางจาก Google, หรือ Paste)
    function loadAndStartCropper(imageSource) {
        $('#dropzoneArea').hide();
        $('#cropperArea').show();
        $('#btnCropApply').show();

        const cropImageEl = document.getElementById('cropImageSource');
        cropImageEl.crossOrigin = 'anonymous'; // ป้องกันปัญหา CORS กรณีรูปจากภายนอก
        cropImageEl.src = imageSource;

        if (cropper) {
            cropper.destroy();
        }

        cropper = new Cropper(cropImageEl, {
            aspectRatio: 1, // บังคับสัดส่วน 1:1 (จัตุรัส/วงกลม)
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.95,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
            checkCrossOrigin: true,
        });
    }

    // 1. เมื่อคลิกปุ่มกล้องถ่ายรูป -> เปิด Modal ขึ้นมาที่หน้า Dropzone ลากวางรูป
    $(document).on('click', '#btnTriggerUpload', function(e) {
        e.preventDefault();
        currentInputFile = document.getElementById('profile_picture');
        clearFileInput(); // เคลียร์เพื่อให้พร้อมเลือกรูปใหม่หรือรูปเดิม
        resetToDropzone();

        const cropModalEl = document.getElementById('modalCropImage');
        if (cropModalEl) {
            let modalInstance = bootstrap.Modal.getInstance(cropModalEl);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(cropModalEl);
            }
            modalInstance.show();
        }
    });

    // 2. ปุ่ม "เลือกรูปภาพจากเครื่อง" ใน Modal
    $(document).on('click', '#btnSelectLocalFile', function() {
        clearFileInput(); // เคลียร์ค่าก่อนเปิดกล่องเลือกไฟล์ เพื่อให้เลือกไฟล์รูปเดิมได้
        $('#profile_picture').trigger('click');
    });

    // 3. ปุ่ม "เปลี่ยนรูปใหม่" ในหน้าจอ Cropper
    $(document).on('click', '#btnCropChangeImage', function() {
        clearFileInput(); // เคลียร์ค่ารูปเดิม
        resetToDropzone();
    });

    // ดักฟังการคลิกที่ input file เผื่อเปิดโดยตรง
    $(document).on('click', '#profile_picture, .image-crop', function() {
        this.value = ''; // รีเซ็ตค่าเสมอเมื่อถูกคลิก
    });

    // 4. ดักฟังการเลือกไฟล์ผ่าน File Dialog ปกติ
    $(document).on('change', '#profile_picture, .image-crop', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('กรุณาเลือกไฟล์ที่เป็นรูปภาพ');
            clearFileInput();
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            loadAndStartCropper(event.target.result);
        };
        reader.readAsDataURL(file);
    });

    // 5. ระบบ Drag & Drop: รองรับการลากรูปจาก Google / คอมพิวเตอร์มาวางในกล่อง Dropzone
    $(document).on('dragover dragenter', '#dropzoneArea', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('bg-white border-success shadow');
    });

    $(document).on('dragleave drop', '#dropzoneArea', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-white border-success shadow');
    });

    $(document).on('drop', '#dropzoneArea', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const dt = e.originalEvent.dataTransfer;

        // กรณีที่ 1: ลากไฟล์ภาพมาจากในเครื่องคอมพิวเตอร์
        if (dt.files && dt.files.length > 0) {
            const file = dt.files[0];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    loadAndStartCropper(event.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                alert('กรุณาวางไฟล์ที่เป็นรูปภาพเท่านั้น');
            }
            return;
        }

        // กรณีที่ 2: ลากรูปภาพมาจากหน้าเว็บ (เช่น ลากรูปจาก Google Images)
        const html = dt.getData('text/html');
        if (html) {
            const regex = /src=["'](.*?)["']/i;
            const match = regex.exec(html);
            if (match && match[1]) {
                loadAndStartCropper(match[1]);
                return;
            }
        }

        // หรือดึงจาก URL ตรงๆ
        const url = dt.getData('text/uri-list') || dt.getData('text/plain');
        if (url && url.match(/\.(jpeg|jpg|gif|png|webp)($|\?)/i)) {
            loadAndStartCropper(url);
            return;3
        }

        alert('ไม่สามารถอ่านรูปภาพนี้ได้ กรุณาลากไฟล์รูปภาพ หรือกดปุ่มเลือกรูปภาพจากเครื่องครับ');
    });

    // เมื่อปิด Modal ให้ล้างข้อมูลหน่วยความจำ
    $(document).on('hidden.bs.modal', '#modalCropImage', function () {
        resetToDropzone();
    });

    // ปุ่มควบคุมหมุนและซูม
    $(document).on('click', '#btnCropRotateLeft', function() {
        if (cropper) cropper.rotate(-90);
    });
    $(document).on('click', '#btnCropRotateRight', function() {
        if (cropper) cropper.rotate(90);
    });
    $(document).on('click', '#btnCropZoomIn', function() {
        if (cropper) cropper.zoom(0.1);
    });
    $(document).on('click', '#btnCropZoomOut', function() {
        if (cropper) cropper.zoom(-0.1);
    });
    $(document).on('click', '#btnCropReset', function() {
        if (cropper) cropper.reset();
    });

    // เมื่อกดปุ่ม "ใช้รูปนี้" -> บีบอัดภาพเหลือ 200x200 px (3-5 KB) และผูกไฟล์เข้ากับฟอร์มเพื่อบันทึก
    $(document).on('click', '#btnCropApply', function() {
        if (!cropper) return;

        let canvas;
        try {
            canvas = cropper.getCroppedCanvas({
                width: 200,
                height: 200,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'medium',
            });
        } catch (err) {
            console.error('Error getting cropped canvas:', err);
            alert('ไม่สามารถประมวลผลรูปภาพนี้ได้ (อาจติดระบบรักษาความปลอดภัย CORS ของเว็บต้นทาง) กรุณาบันทึกรูปลงเครื่องแล้วเลือกใหม่อีกครั้งครับ');
            return;
        }

        if (!canvas) {
            alert('เกิดข้อผิดพลาดในการตัดรูปภาพ กรุณาลองใหม่อีกครั้ง');
            return;
        }

        try {
            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert('ไม่สามารถสร้างไฟล์รูปภาพได้ กรุณาลองใหม่อีกครั้ง');
                    return;
                }

                console.log('ขนาดไฟล์รูปหลังบีบอัด:', (blob.size / 1024).toFixed(2) + ' KB');

                const fileName = 'avatar_' + Date.now() + '.jpg';
                const croppedFile = new File([blob], fileName, { type: 'image/jpeg' });

                // ผูกไฟล์รูปที่ตัดแล้วเข้าไปยัง input file ของฟอร์ม เพื่อให้ตอนกด Submit ส่งไปบันทึกลง public/profile_image
                const fileInput = document.getElementById('profile_picture') || currentInputFile;

                if (fileInput) {
                    try {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(croppedFile);
                        fileInput.files = dataTransfer.files;
                        console.log('✅ ผูกไฟล์รูปเข้ากับ input สำเร็จ:', fileInput.files[0].name, (fileInput.files[0].size / 1024).toFixed(2) + ' KB');
                    } catch (dtErr) {
                        console.warn('DataTransfer error:', dtErr);
                    }
                }

                // นำรูปไปแสดงตัวอย่างในวงกลมหน้าฟอร์มทันที
                const previewUrl = canvas.toDataURL('image/jpeg', 0.6);
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                if (preview) {
                    preview.src = previewUrl;
                    preview.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                // ปิด Modal ตัดรูป
                const cropModalEl = document.getElementById('modalCropImage');
                if (cropModalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(cropModalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            }, 'image/jpeg', 0.6);
        } catch (toBlobErr) {
            console.error('Error in toBlob:', toBlobErr);
            alert('ไม่สามารถประมวลผลรูปภาพได้ เนื่องจากติดระบบ CORS ของภาพต้นทาง กรุณาบันทึกรูปลงเครื่องก่อนแล้วเลือกไฟล์ครับ');
        }
    });


    // =========================================================
    //  ระบบคลิกดูรูปโปรไฟล์ขนาดใหญ่ (View Full Image)
    // =========================================================
    $(document).on('click', '#avatarContainer, #imagePreview, .view-image-trigger', function(e) {
        // หา URL ของรูปภาพ
        let imgSrc = '';

        if ($(this).is('img')) {
            imgSrc = $(this).attr('src');
        } else if ($(this).find('img').length > 0) {
            imgSrc = $(this).find('img').attr('src');
        } else {
            const preview = document.getElementById('imagePreview');
            if (preview) imgSrc = preview.src;
        }

        // ถ้าไม่มีรูป หรือเป็นค่าว่าง / ค่า placeholder '#' ไม่ต้องเปิด
        if (!imgSrc || imgSrc.includes('#') || imgSrc.trim() === '') {
            return;
        }

        const viewImageEl = document.getElementById('viewImageTarget');
        const viewModalEl = document.getElementById('modalViewImage');

        if (viewImageEl && viewModalEl) {
            viewImageEl.src = imgSrc;
            let viewModal = bootstrap.Modal.getInstance(viewModalEl);
            if (!viewModal) {
                viewModal = new bootstrap.Modal(viewModalEl);
            }
            viewModal.show();
        }
    });

