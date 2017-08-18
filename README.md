# multiImageUploadForm
multi Image Upload Form using php , javascript , html , css 

**********

### 기본 설명
원하는 위치에 아래의 form을 붙여넣고 id를 원하는 형식으로 변경한다


    <form id="logoImage" method="post" enctype="multipart/form-data">
        <div class="imageUploadWrapper">
        </div>
        <script id="imageUploadWrapperBox" type='text/template'>
            <div class="imageUploadWrapperBox">
                <div class="js--image-preview"></div>
                <div class="upload-options upload-options-left">
                    <label>
                        <input name="imageFile[]" type="file" class="image-upload" accept="image/*" />
                    </label>
                </div>
                <div class="upload-options upload-options-right">
                    <label>
                    </label>
                </div>
            </div>
        </script>
    </form>
  
  
###  setImageUploadForm(formID, imageNum)
이미지폼을 세팅합니다. 폼 아이디와 원하는 이미지의 갯수를 입력합니다.
###### <i class="icon-pencil"></i> 예시) setImageUploadForm("#logoImage", 3);


###  setInitImage(formID, imageUrlArr)
이미지 url 배열을 넣으면, 이미지들을 초기화 한다.
###### <i class="icon-pencil"></i> 예시) setInitImage("#clientImage", [ "http://04banjang.co.kr/yfmp/image/"+ memberData['ITEM']['CM_PROFILE_IMG'] ]);
