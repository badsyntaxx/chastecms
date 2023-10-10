/**
 * Filebrowser
 *
 * The filebrowser plugin allows you to browse file on the server.
 * Along the filebrowser php libraries it can create new folders 
 * and with the help of dropzone upload new files.
 * the inputs you want by setting the option to true.
 *
 * Example
 *     $('#elem').filebrowser({
 *         dir : '/root/path/dir/',
 *     }); 
 */
(function($) {

    $.fn.filebrowser = function(options) {

        var settings = $.extend({
            dir : null
        }, options);

        var action = {

            setCurrentDir: function(dir) {
                localStorage.current_path = null;
                if (localStorage.current_path === 'null') {
                    localStorage.current_path = dir;
                }
            },

            getFiles: function(dir) {

                action.setCurrentDir(dir);

                $('.alert').remove();
                $('.fb-dirs').html('');
                $('.fb-files').html('');
                $('#new-folder-dir').val(dir);
                $('#upload-dir').val(dir);
                $('.fb-footer').text(dir);

                $.ajax({
                    url: '/admin/filebrowser/browse',
                    type: 'POST',
                    data: {dir: dir},
                    dateType: 'json',
                    beforeSend: function() {
                        $('.filebrowser').append('<div class="loading"><div class="loading-spinner"><img src="/views/images/loading.svg" alt="Loading" class="row"></div></div>');
                    },
                    success: function(response) {
                        $('.btn-browse').removeClass('disabled').html('<i class="fa fa-folder fa-fw"></i> Browse');
                        $('.btn-close').removeClass('disabled').html('<i class="fa fa-times fa-fw"></i>');
                        $('.loading').remove();
                        if ($.trim(response)) {
                            var data = jQuery.parseJSON(response);
                            var dirs = data.dirs;
                            var dirs_num = 0;
                            var files = data.files;
                            var files_num = 0;
                            $(dirs).each(function() {
                                $('.fb-dirs').append('<li class="fb-item fb-dir"><i class="fas fa-folder fa-fw"></i> <a href="#" class="fb-dir-link"></a><button type="button" class="fb-btn-delete"><i class="fas fa-trash fa-fw"></i></button></li>');
                            });
                            $(files).each(function() {
                                $('.fb-dirs').append('<li class="fb-item fb-file"><a href="#" class="fb-file-link"></a><button type="button" class="fb-btn-delete"><i class="fas fa-trash fa-fw"></i></button></li>');
                            });
                            $('.fb-dir-link').each(function() { 
                                $(this).text(dirs[dirs_num]); 
                                dirs_num++; 
                            });
                            $('.fb-file-link').each(function() {
                                $(this).text(files[files_num]);
                                files_num++;
                            });
                            $('.fb-file').each(function() {
                                var filename = $(this).text();
                                var extension = filename.substr((filename.lastIndexOf('.') +1));
                                var ext_ico = action.getExtension(extension);
                                switch (extension) {
                                    case 'jpg':
                                    case 'JPG':
                                    case 'jpeg':
                                    case 'JPEG':
                                    case 'png':
                                    case 'PNG':
                                    case 'gif':
                                    case 'GIF':
                                        $(this).prepend('<div class="fb-thumbnail"><img src="' + localStorage.current_path + filename + '"></div> ');
                                        break;
                                    default:
                                        $(this).prepend(ext_ico + ' ');
                                        break;
                                }
                            });
                        } 
                    }
                });
            },

            descend: function(path) {
                var foldername = $(path).text().trim();
                var dir = localStorage.current_path + foldername + '/';
                action.getFiles(dir);
                $('#new-folder-dir').val(dir);
                $('#upload-dir').val(dir + '/');
                $('.fb-btn-back').css({'display' : 'block'});
                dir = dir.split('/');
                dir = dir[dir.length -2];
                $('.filebrowser .title').text('File Browser: ' + dir);
            },

            ascend: function() {
                var current_path = localStorage.current_path;
                var dirs = current_path.split('/');
                var current_dir_name = dirs[dirs.length -2];
                var main_dir = settings.dir;
                $('.filebrowser .title').text('File Browser: ' + current_dir_name);
                dirs = dirs.slice(0, -2);
                dir = dirs.join('/');
                localStorage.current_path = dir + '/';
                action.getFiles(localStorage.current_path);
                $('#new-folder-dir').val(localStorage.current_path);
                $('#upload-dir').val(localStorage.current_path);
                if (localStorage.current_path == main_dir) {
                    $('.fb-btn-back').css({'display' : 'none'});
                }
            },

            makeFolder: function() {
                var dir = $('#new-folder-dir').val();
                $.ajax({
                    url: '/admin/filebrowser/makeFolder',
                    type: 'POST',
                    data: $('#new-folder-form').serialize(),
                    beforeSend: function() {
                        $('.filebrowser').append('<div class="loading"><div class="loading-spinner"><img src="/views/images/loading.svg" alt="Loading" class="row"></div></div>');
                    },
                    success: function(response, status, xhr) {
                        if ($.trim(response)) {
                            $('.loading').remove();
                            action.getFiles(dir);
                            $('.fb-fileview').prepend(response);
                        } 
                    }
                });
            },

            delete: function(dir, file) {
                $.ajax({
                    url: '/admin/filebrowser/delete',
                    type: 'POST',
                    data: {dir: dir, file: file},
                    dateType: 'json',
                    beforeSend: function() {
                        
                    },
                    success: function(response, status, xhr) {
                        if ($.trim(response)) {
                            action.getFiles(dir);
                            $('.fb-fileview').prepend(response);
                        } 
                    }
                });
            },

            getExtension: function(extension) {
                switch (extension) {                       
                    case 'zip':
                    case 'rar':
                        return '<i class="fas fa-file-archive-o fa-fw"></i>';
                    break;
                    case 'pdf':
                        return '<i class="fas fa-file-pdf-o fa-fw"></i>';
                    break;
                    case 'htm':
                    case 'html':
                        return '<i class="fab fa-html5 fa-fw"></i>';
                    break;
                    case 'php':
                        return '<i class="fas fa-code fa-fw"></i>';
                    break;
                    case 'css':
                        return '<i class="fas fa-css3 fa-fw"></i>';
                    break;
                    case 'xlsx':
                        return '<i class="fas fa-file-excel-o fa-fw"></i>';
                    break;
                    case 'docx':
                        return '<i class="fas fa-file-word-o fa-fw"></i>';
                    break;
                    default:
                        return '<i class="fas fa-question-circle-o fa-fw"></i>';
                }
            }
        }

        return this.each(function() {

            $(this).append('<div class="filebrowser"><div class="fb-titlebar"><span class="title">File Browser</span> <button type="button"class="btn btn-close pull-right"><i class="fas fa-times fa-fw"></i></button></div><div class="fb-actions"><button type="button"class="btn btn-go fb-btn-new-folder no-load" title="New Folder"><i class="fas fa-plus fa-fw"></i></button><button type="button"class="btn btn-default fb-btn-upload no-load" title="Upload"><i class="fas fa-upload fa-fw"></i></button><button type="button"class="btn btn-default fb-btn-refresh no-load" title="Refresh"><i class="fas fa-sync-alt fa-fw"></i></button><button type="button"class="btn btn-default fb-btn-back no-load" title="Back"><i class="fas fa-reply fa-fw"></i></button></div><div class="fb-fileview"><ul class="list-unstyled fb-dirs"></ul><ul class="list-unstyled fb-files"></ul></div><div class="fb-footer"></div><div class="fb-upload"><button type="button"class="btn fb-btn-close-upload no-load"><i class="fas fa-times fa-fw"></i></button><form action=""class="dropzone needsclick dz-clickable"id="dropzone"><input type="hidden"name="upload_dir"value=""id="upload-dir"></form></div><div class="fb-new-folder"><form enctype="multipart/form-data"id="new-folder-form"><div class="row form-row"><label for="foldername">New folder</label><div class="row"><input type="hidden"name="new_folder_dir"value=""id="new-folder-dir"><input type="text"name="foldername"class="fb-new-folder-input"><button type="button"class="btn-go fb-btn-make-folder"><i class="fas fa-save fa-fw"></i> Save</button></div></div></form></div></div>');

            $('body').on('click', '.btn-browse', function() {
                $('.filebrowser').css({'display': 'block'});
                action.getFiles(settings.dir);
            });

            $('.filebrowser').on('click', '.btn-close', function() { 
                $('.filebrowser').css({'display': 'none'}); 
            });

            $('.filebrowser').on('click', '.fb-btn-refresh', function() { 
                $('.fb-new-folder').removeClass('open');
                action.getFiles(localStorage.current_path); 
            });

            $('.filebrowser').on('click', '.fb-btn-new-folder', function() { 
                $('.fb-new-folder').toggleClass('open'); 
            });

            $('.fb-new-folder').on('click', '.fb-btn-make-folder', function() {
                action.makeFolder();
            });

            $('#new-folder-form').keypress(function(e) {
                if (e.which == 13) {
                    $('.fb-btn-make-folder').click();
                    return false;
                }
            });

            $('.filebrowser').on('click', '.fb-btn-upload', function() {
                $('.fb-upload').toggleClass('open');
            });

            $('.fb-upload').on('click', '.fb-btn-close-upload', function() {
                $('.fb-upload').removeClass('open');
                action.getFiles(localStorage.current_path); 
            });

            $('.filebrowser').on('click', '.fb-dir-link', function(e) {
                e.preventDefault();
                action.descend($(this));
            });

            $('.fb-btn-back').click(function() {
                action.ascend();
            });

            $('.filebrowser').on('click', '.fb-btn-delete', function() {
                var path = localStorage.current_path;
                var parent = $(this).parent().get(0);
                var file = $(parent).text();
                action.delete(path, file);
            });
        });
    }
}(jQuery));