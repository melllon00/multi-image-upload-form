function setImageUploadForm(formID, imageNum){

    for ( var i = 0 ; i < imageNum ; i++){
        $(formID + " .imageUploadWrapper").append($('#imageUploadWrapperBox').html());
    }

    // initialize box-scope
    var boxes = document.querySelectorAll(formID+ ' .imageUploadWrapperBox');

    for(let i = 0; i < boxes.length; i++) {
        let box = boxes[i];
        initDropEffect(box);
        initImageUpload(box);
    }
}


function initImageUpload(box) {
    let uploadField = box.querySelector('.image-upload');
    let imgDeleBtn = box.querySelector('.upload-options-right');

    uploadField.addEventListener('change', getFile);
    imgDeleBtn.addEventListener('click', imgDel);

    function getFile(e){
        let file = e.currentTarget.files[0];
        checkType(file);
    }

    function imgDel(){
        $(box).children('.upload-options-left').children().children().val("");
        $(box).children('.js--image-preview').css('background-image', '');
        $(box).children('.js--image-preview').removeClass('js--no-default');
    }

    function previewImage(file){
        let thumb = box.querySelector('.js--image-preview'),
            reader = new FileReader();

        reader.onload = function() {
            thumb.style.backgroundImage = 'url(' + reader.result + ')';
        }
        reader.readAsDataURL(file);
        thumb.className += ' js--no-default';
    }

    function checkType(file){
        let imageType = /image.*/;
        if (!file.type.match(imageType)) {
            throw 'Datei ist kein Bild';
        } else if (!file){
            throw 'Kein Bild gewählt';
        } else {
            previewImage(file);
        }
    }

}

/// drop-effect
function initDropEffect(box){
    let area, drop, areaWidth, areaHeight, maxDistance, dropWidth, dropHeight, x, y;

    // get clickable area for drop effect
    area = box.querySelector('.js--image-preview');
    area.addEventListener('click', fireRipple);

    function fireRipple(e){
        area = e.currentTarget
        // create drop
        if(!drop){
            drop = document.createElement('span');
            drop.className = 'drop';
            this.appendChild(drop);
        }
        // reset animate class
        drop.className = 'drop';

        // calculate dimensions of area (longest side)
        areaWidth = getComputedStyle(this, null).getPropertyValue("width");
        areaHeight = getComputedStyle(this, null).getPropertyValue("height");
        maxDistance = Math.max(parseInt(areaWidth, 10), parseInt(areaHeight, 10));

        // set drop dimensions to fill area
        drop.style.width = maxDistance + 'px';
        drop.style.height = maxDistance + 'px';

        // calculate dimensions of drop
        dropWidth = getComputedStyle(this, null).getPropertyValue("width");
        dropHeight = getComputedStyle(this, null).getPropertyValue("height");

        // calculate relative coordinates of click
        // logic: click coordinates relative to page - parent's position relative to page - half of self height/width to make it controllable from the center
        x = e.pageX - this.offsetLeft - (parseInt(dropWidth, 10)/2);
        y = e.pageY - this.offsetTop - (parseInt(dropHeight, 10)/2) - 30;

        // position drop and animate
        drop.style.top = y + 'px';
        drop.style.left = x + 'px';
        drop.className += ' animate';
        e.stopPropagation();

    }
}


function findNotChangedImage(formID){
    var nonchangeUrlArray = [];
    var previews = $(formID + ' .js--image-preview');

    for ( var i = 0 ; i < previews.length ; i++ ){
        // preview 상태이면 이미지의 value 를 측정한다.
        if(  previews.eq(i).hasClass('js--no-default')  ){
            switch ( previews.eq(i).css('background-image').substr(5,4) ){
                // 원래 이미지
                case "http":
                    nonchangeUrlArray.push( previews.eq(i).css('background-image').split("image/")[1].slice(0, -2) ) ;
                    break;
                // 새로 추가된 이미지
                case "data":
                    break;
                default:
                    break;
            }
        }
    }
    return nonchangeUrlArray;
}

function getNewImageUrls(formID, imageUploadJson){
    var newImgUrls = findNotChangedImage(formID);

    if ( imageUploadJson['allStatus'] == "OK"){
        for ( var i = 0 ; i < imageUploadJson['ITEM'].length ; i++ ){
            if ( imageUploadJson['ITEM'][i]['filename'] != "")
                newImgUrls.push(imageUploadJson['ITEM'][i]['filename'].split('image/')[1]);
        }
    }
    return newImgUrls;
}

function setInitImage(formID, imageUrlArr){
    // initialize box-scope
    var boxes = document.querySelectorAll(formID+ ' .imageUploadWrapperBox');

    for(let i = 0; i < imageUrlArr.length; i++) {
        let box = boxes[i];
        $(box).children('.upload-options-left').children().children().val("");

        $(box).children('.js--image-preview').css('background-image', "url('"+ imageUrlArr[i] +"')");
        $(box).children('.js--image-preview').addClass('js--no-default');
    }
}



// 사진 업로드 메소드
function imgUploadfun(formID) {
    var imageUploadJson;
    var sendData = new FormData($(formID)[0]);
    var pattern = /[^(0-9)]/gi; // 숫자이외는 제거

    // 윈도우인지 다른 브라우저인지 확인
    var ua = window.navigator.userAgent;
    var postData;
    // 윈도우라면 ?
    if (ua.indexOf('MSIE') > 0 || ua.indexOf('Trident') > 0) {
        // postData = encodeURI(sendData);
        postData = sendData;
    } else {
        postData = sendData;
    }

    $.ajax({
        url: "../phpFunc/logoImageUpload.php", // Url to which the request is send
        type: "POST", // Type of request to be send, called as method
        data: postData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false, // The content type used when sending data to the server.
        cache: false, // To unable request pages to be cached
        processData: false, // To send DOMDocument or non processed data file it is set to false
        async: false,
        success: function(json, status) // A function to be called if request succeeds
        {
            imageUploadJson = JSON.parse(json);
        },
        error: function(e) {
            alert("error");
        }
    });

    return imageUploadJson;
}