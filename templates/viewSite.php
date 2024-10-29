<div class="wrap aimtell active-site">
    <div class="centerfloat">
        <div class="heading">
        	
        	<span class="fa-stack fa-lg">
			  <i class="fa fa-circle fa-inverse fa-stack-2x"></i>
			  <i class="fa fa-stack-1x fa-check"></i>
			</span>
            <span class="title">Installation Complete</span>
        </div>
        <div class="go-to-site">
            <p>To view your subscribers and start sending push notifications please <a target="_BLANK" href="https://dashboard.aimtell.com/login/auth/<?php echo $aimtell_auth_token;?>">continue to your dashboard.</a> </p>
        </div>

    </div>
</div>

<script>
//make sure the token is still valid.
token = "<?php echo $aimtell_auth_token ;?>";
result = validateAuthToken(token);
jQuery.when(result).done(function(data){
    if(data.result == "error"){
        _aimtellDeleteCookie("aimtell_auth_token") // clear bad token
        gotoUrl(window.location.href, {'page':'login'});
    }
});

</script>
