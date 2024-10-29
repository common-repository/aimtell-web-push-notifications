<div class="wrap aimtell">
    <div class="centerfloat">
        <div align="center" class="logo">
            <img src="<?php echo AIMTELL_URL . 'assets/images/logo_white.png' ?>"  />
        </div>
        <div class="loginBox">
            <div class="heading">
                <span class="title"> Let's Prepare Your Website</span>
                <span class="byline"> Enter the information below </span>
            </div>
    		<div align="center" class="sitelogo">
    			<img style="background-image: url('<?php echo AIMTELL_URL . 'assets/images/sample-push-icon.jpg' ?>');" src="<?php echo AIMTELL_URL . 'assets/images/sample-push-icon.jpg' ?>" class="imgpreview group">
                <button id="uploadImg" type="button" class="btn btn-default"> Add Site Image </button>
                <span class="icon-info"> (used as icon in push notification) </span>
    			<form enctype="multipart/form-data" id="iconUpload">
    				<input style="display:none;" id="websiteimage" type="file" name="icon" class="form-control">
    			</form>
    		</div>
            Site Name: 
            <input class="form-input" type="text" name="sitename" id="websitename">
            <input type="hidden" id="websiteurl" value="<?php echo AIMTELL_CURSITE_URL; ?>">
            <div class="row">
                <div class="col-md-12">
                    <button id="addWebsite" type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="errorBox"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- page specific js -->
<script>

    //update website image preview 
    jQuery("#websiteimage").change(function(event) {
        input = jQuery(this).prop('files')[0];
        var FR = new FileReader();
        FR.onload = function(e) {
             jQuery('.imgpreview').attr( "src", e.target.result );
             jQuery('.imgpreview').css("background-image", "none").css("background", "#FFF");
        }
        FR.readAsDataURL( input );
    });


    jQuery("#uploadImg").click(function(event) {
        jQuery('#websiteimage').click()
    });

    //add website 
    jQuery("#addWebsite").click(function(event) {
        
        //clear again
        jQuery(".errorBox").text("");
        jQuery(".errorBox").hide();

        websitename = jQuery("#websitename").val() 
        websiteurl = jQuery("#websiteurl").val()

        //if no image has been loaded, alert user
        uploadimage = jQuery("#websiteimage");

        if (uploadimage.val() == '') {
            jQuery(".errorBox").text("An icon is required.");
            jQuery(".errorBox").show();
            return false;
        }
        else if(websitename.length < 1){
            jQuery(".errorBox").text("Missing site name.");
            jQuery(".errorBox").show();
            return false;   
        }

        //notify user on first time that HTTPS is required for chrome notifications
        if(websiteurl.indexOf("https") == -1){

        //if it has the class warned, then we dont need to show this again
        if(!jQuery(".errorBox").hasClass("warned")){
            jQuery(".errorBox").addClass("warned") //add the fact that we ahve alreayd warned the user.
            jQuery(".errorBox").show();
            jQuery(".errorBox").text("Please note that HTTPS is required for chrome push notifications. You may still continue but only safari push notifications will work.");
            return false;   
        }
            

        }

        //acceptable image requirements
        acceptableFormats = ["image/png", "image/jpeg", "image/jpeg"]
        imageFormat = uploadimage[0].files[0].type
        imageSize = uploadimage[0].files[0].size
        
        //if the image is larger than 1MB , tell the user to shrink the image down
        if(imageSize > 1000000){
            jQuery(".errorBox").text("Image file is too large, please upload a file that is < than 1MB");
            jQuery(".errorBox").show();
            return false;
        }

        //if the image is not an acceptable format
        if(acceptableFormats.indexOf(imageFormat) < 0){
            jQuery(".errorBox").text("Unsupported image file type. Please upload .jpg or .png file.");
            jQuery(".errorBox").show();
            return false;
        }

        jQuery("#addWebsite").prop("disabled", true);
        jQuery("#addWebsite").text("Adding site...");

        add = addWebsite(websitename, websiteurl)
        jQuery.when(add).done(function(data){

            //grab the site info
            var idSite = data.id;
            var domain = data.url;
            var webPushID = data.webPushID;
            var uid = data.uid;

            //add the icon
            icon = uploadIcon(idSite);
            jQuery("#addWebsite").text("Uploading icon...");

            jQuery.when(icon).done(function(data){

                //generate the push package
                pushpackage = generatePushPackage(idSite);
                jQuery("#addWebsite").text("Preparing files ...");
                jQuery.when(pushpackage).done(function(data){      

                    jQuery("#addWebsite").text("Finalizing assets ...");

                    //grab final details of site
                    site = getWebsite(idSite);
                    jQuery.when(site).done(function(siteInfo){
                        gotoUrl(window.location.href, {'page':'viewSite', 'idSite': siteInfo.id, 'domain': siteInfo.url, 'webPushID': siteInfo.webPushID, 'uid': siteInfo.uid});
                    });
                    

                });
              
            });

        });

    });

</script>