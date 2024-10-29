<div class="wrap aimtell login">
    <div class="centerfloat">
        <div align="center" class="logo">
            <img src="<?php echo AIMTELL_URL . 'assets/images/logo_white.png' ?>"  />
        </div>
        <div class="loginBox">
            <div class="heading">
                <span class="title"> Already a user?</span>
                <span class="byline"> Sign in to your account. </span>
            </div>
            <div class="login-general">
              Username: <input class="form-input" type="text" name="username">
              Password: <input class="form-input" type="password" name="password">
            </div>
            <div class="login-twofactor" style="display:none;">
              Two Factor Code: <input autocomplete="off" id="two_factor" type="text" class="form-input">
            </div>
            <div class="clearfix">
                <div class="pull-right">
                    <button id="loginSubmit" type="button" class="btn btn-primary"> Sign In</button>
                </div>
            </div>
            <div class="errorBox">
            </div>
        </div>
        <div align="center" class="signup-text"> <span> Not yet a user? <a style="color:#FFF" target="_BLANK" href="https://aimtell.com/trial?utm_source=wordpress&utm_medium=plugin">Click to here create an account. </a></span> </div>
    </div>
</div>


<!-- page specific js -->
<script>

  jQuery("#loginSubmit").click(function(e){


    //check to make sure fields are all ok 
    jQuery(".errorBox").html("") //reset error
    jQuery(".errorBox").hide() //reset error
    jQuery(".form-input").removeClass("errorBorder");
    ready = true; //set default

    //check username length
    if(jQuery("input[name=username]").val().length < 1){
      jQuery("input[name=username]").addClass("errorBorder");
      ready = false;
    }
    //check password length
    else if(jQuery("input[name=password]").val().length < 1){
      jQuery("input[name=password]").addClass("errorBorder");
      ready = false;
    }

    //if not ready, error out.
    if(ready == false){
      jQuery(".errorBox").html("Please make sure all the fields are filled out").show();
      return false;
    }

    jQuery("#loginSubmit").text("Logging in...")
    jQuery("#loginSubmit").prop("disabled", true)

    //grab login credentials
    username = jQuery("input[name=username]").val();
    password = jQuery("input[name=password]").val();
    two_factor = jQuery("#two_factor").val();

    if(two_factor.length > 0){
      attempt = login(username, password, two_factor);
    }
    else{
      attempt = login(username, password);
    }

    jQuery.when(attempt).done(function(data){
      //if login was a success, set cookies and reload page
      if(data.result == "success"){
        
        jQuery("#loginSubmit").text("Loading site...")

        //store token
        _aimtellSetCookie('aimtell_auth_token', data.auth_token, 7);

        //check if current website already exists
        result = checkCurrentSiteExists();
        jQuery.when(result).done(function(siteInfo){
            //if site exists, go to viewSite
            if(siteInfo){
                gotoUrl(window.location.href, {'page':'viewSite', 'idSite': siteInfo.id, 'domain': siteInfo.url, 'webPushID': siteInfo.webPushID, 'uid': siteInfo.uid});
            }
            //if site does not exist, go to createSite
            else{
                gotoUrl(window.location.href, {'page':'addSite'});
            }
        });
      }
      //unable to login, show the error
      else if(data.result == "error"){

        jQuery("#loginSubmit").text("Login")
        jQuery("#loginSubmit").prop("disabled", false)

        if(data.error_code == 221){
          jQuery(".login-general").hide();
          jQuery(".login-twofactor").show();
          return false;
        }

        jQuery(".errorBox").html(data.message);
        jQuery(".errorBox").show();
        return false;
      }

    });
    
  })

</script>