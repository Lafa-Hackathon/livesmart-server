<?php
include_once 'header.php';
?>
<h1 class="h3 mb-2 text-gray-800" data-localize="meeting_videoai"></h1>
<div id="error" style="display:none;" class="alert alert-danger"></div>
<div id="success" class="alert alert-success" style="display: none;"></div>
<?php if ($_SESSION["tenant"] == 'lsv_mastertenant' || $_SESSION["tenant_admin"]) {
?>

    <div class="row">
        <div class="col-lg-6">
            <div class="p-1">
                <h4 data-localize="avatars_ai"></h4>
                <p data-localize="avatars_ai_info"></p>
                <fieldset style="padding-left:10px;">
                    <div class="form-group" id="avatar_container"></div>
                </fieldset>
                <hr>
                <h4 data-localize="video_back_color"></h4>
                <fieldset style="padding-left:10px;">
                    <div class="form-group">
                        <label for="video-element-back-img" data-localize="or_choose_image">></label>
                        <input type="file" accept=".jpg, .jpeg, .png" class="form-control" name="video-element-back-img" id="video-element-back-img" value="" />
                    </div>
                    <span data-localize="orchoose_images"></span>
                    <div class="form-group" id="video-element-back-images"></div>
                    <div class="form-group">
                        <img id="video-element-back-preview" src="" width="200" />
                    </div>
                </fieldset>
                <div class="form-group">
                    <h6 data-localize="room_ai"></h6>
                    <input type="text" autocomplete="off" class="form-control" id="room" name="room" aria-describedby="room">
                </div>
                <div class="form-group">
                    <h6 data-localize="avatar_system"></h6>
                    <textarea class="form-control" rows="4" id="system" name="system">You are an avatar from LiveSmart Server Video, leading company that specialize in videos commnucations. Please try answer the questions or respond their comments naturally, and concisely. Please try your best to response with short answers and answer the last question.</textarea>
                </div>
                <div class="form-group">
                    <h6 data-localize="quality_ai"></h6>
                    <select class="form-control" name="quality" id="quality">
                        <option value="low">low</option>
                        <option value="medium" selected>medium</option>
                        <option value="high">high</option>
                    </select>
                </div>
                <h4 data-localize="advanced_tools"></h4>
                <fieldset style="padding-left:10px;">
                    <h6 data-localize="advanced_tools_info"></h6>
                    <div class="form-group">
                        <h6 data-localize="tools_name"></h6>
                        <input type="text" autocomplete="off" class="form-control" id="tools_name" name="tools_name" aria-describedby="room">
                    </div>
                    <div class="form-group">
                        <h6 data-localize="tools_description"></h6>
                        <input type="text" autocomplete="off" class="form-control" id="tools_description" name="tools_description" aria-describedby="room">
                    </div>
                    <div class="form-group">
                        <h6 data-localize="tools_parameters"></h6>
                        <input type="text" autocomplete="off" class="form-control" id="tools_parameters" name="tools_parameters" aria-describedby="room">
                    </div>
                </fieldset>
                <a href="javascript:void(0);" id="saveAvatars" class="btn btn-primary" data-localize="save">
                </a>
                <input type="hidden" class="form-control" value="" id="video-element-back-hidden">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="p-1">
                <video src="" controls="yes" width="100%" id="videoPreview"></video>
            </div>
        </div>
    </div>
    <script>
        const previewPhoto = (elem, input) => {
            const file = input.files;
            if (file) {
                const fileReader = new FileReader();
                var preview = elem;
                fileReader.onload = event => {
                    preview.setAttribute('src', event.target.result);
                }
                fileReader.readAsDataURL(file[0]);
            }
        }
        const input = document.getElementById('video-element-back-img');
        input.addEventListener('change', function() {
            previewPhoto(document.getElementById('video-element-back-preview'), input);
        });

        const folder = 'img/virtual/';

        function handleBacks(id, elem) {
            document.getElementById(elem + '-preview').setAttribute('src', '../' + folder + id);
            $('#' + elem + '-hidden').val(folder + id);
        }
        const backImages = document.getElementById('video-element-back-images');
        for (i = 1; i <= 20; i++) {
            let img = document.createElement('img');
            img.setAttribute('src', '../' + folder + i + '.jpg');
            img.setAttribute('id', i + '.jpg');
            img.setAttribute('width', '80');
            img.setAttribute('style', 'cursor:pointer; padding: 2px;');
            backImages.append(img);
            img.addEventListener('click', function() {
                handleBacks(img.id, 'video-element-back');
            });
        }
    </script>


<?php } else {
    header("Location: dash.php");
    die();
} ?>
<?php
include_once 'footer.php';
