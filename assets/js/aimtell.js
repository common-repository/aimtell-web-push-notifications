/**
 * functions
 */

var at_api = "https://api.aimtell.com/prod";
var auth_token = _aimtellGetCookie("aimtell_auth_token")


/**
 * uploads a website icon
 * @param  {[int]} idSite [website id]
 * @return {[type]}        [description]
 */
function uploadIcon(idSite){

  var deferred = jQuery.Deferred();

  //if no image has been loaded, skip this
  var uploadimage = jQuery("#websiteimage");
  if (uploadimage.val() == '') {
    return false;
  }

  //grab formData
  var formData = new FormData(document.querySelector('#iconUpload'))

    jQuery.ajax({
        type: 'post',
        url: at_api+'/site/icon/'+idSite,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function (request)
        {
            request.setRequestHeader("X-Authorization", auth_token);
        },
        success: function (data) {
          console.log(data)
          deferred.resolve(data);
        }
      });

      return deferred.promise();

}


function addWebsite(name, url)
{

    var deferred = jQuery.Deferred();
    var result;

    formData = {};
    formData.url = url;
    formData.name = name;
    formData = JSON.stringify(formData);

    //get the user
    jQuery.ajax({
    url : at_api+"/sites/",
    type: "POST",
    dataType: 'json',
    data : formData,
    beforeSend: function (request)
        {
            request.setRequestHeader("X-Authorization", auth_token);
        },
    success: function(data, textStatus, jqXHR)
        {
          console.log("success");
          deferred.resolve(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          console.log("error");
          console.log(errorThrown);
        }
      });

  return deferred.promise();
}



function generatePushPackage(idSite){

  var deferred = jQuery.Deferred();
  var result;

  jQuery.ajax({
  url : at_api+"/push/package/"+idSite,
  dataType: 'json',
  type: 'POST',
  beforeSend: function (request)
      {
          request.setRequestHeader("X-Authorization", auth_token);
      },
  success: function(data, textStatus, jqXHR)
      {
        console.log("success");
        console.log(data)
        deferred.resolve(data);
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        console.log("error");
        console.log(errorThrown);
      }
    });

  return deferred.promise();

}


/**
 * logs into aimtell with supplied username and password
 * @param  {[type]} username [description]
 * @param  {[type]} password [description]
 * @return {[type]}          [description]
 */
function login(username, password, two_factor){

  var deferred = jQuery.Deferred();

  formData = {};
  formData.username = username
  formData.password = password
  if(two_factor){
    formData.two_factor = two_factor;
  }
  formData = JSON.stringify(formData);

  jQuery.ajax({
  url : at_api+"/login",
  type: "POST",
  dataType: 'json',
  data : formData,
  success: function(data, textStatus, jqXHR)
      {
        console.log("success");
        console.log(data)
        deferred.resolve(data);
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        console.log("error");
        console.log(errorThrown);
      }
    });

  return deferred.promise();

}

/**
 * grabs cookie
 * @param  {[string]} cname [name of cookie]
 * @return {[string]}       [value of cookie]
 */
function _aimtellGetCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

/**
 * sets cookie
 * @param {[string]} cname  [cookie name]
 * @param {[string]} cvalue [value of cookie]
 * @param {[string]} exdays [time to expire]
 */
function _aimtellSetCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; path=/; " + expires;
}

/**
 * deletes cookie
 * @param  {[string]} cname [name of cookie]
 */
function _aimtellDeleteCookie(cname){
  document.cookie = cname + "= ; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC";
}

/**
 * grab all websites information that user has permission for
 * @return {[type]} [description]
 */
function getWebsites(){
  
  var deferred = jQuery.Deferred();

  //delcare token in case its called before reload
  auth_token = _aimtellGetCookie("aimtell_auth_token")

  jQuery.ajax({
  url : at_api+"/sites/",
  dataType: 'json',
  beforeSend: function (request)
      {
        request.setRequestHeader("X-Authorization", auth_token);
      },
  success: function(data, textStatus, jqXHR)
      {
        console.log(data)
        deferred.resolve(data);
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        console.log("error");
        console.log(errorThrown);
      }
    });

  return deferred.promise();

}

/**
 * grab website based on ID
 * @return {[type]} [description]
 */
function getWebsite(idSite){
  
  var deferred = jQuery.Deferred();

  //delcare token in case its called before reload
  auth_token = _aimtellGetCookie("aimtell_auth_token")

  jQuery.ajax({
  url : at_api+"/site/"+idSite,
  dataType: 'json',
  beforeSend: function (request)
      {
        request.setRequestHeader("X-Authorization", auth_token);
      },
  success: function(data, textStatus, jqXHR)
      {
        console.log(data)
        deferred.resolve(data);
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        console.log("error");
        console.log(errorThrown);
      }
    });

  return deferred.promise();

}

/**
 * checks to see if the current website already exists for current user
 * @return {int} 0 if not found, else idSite
 */
function checkCurrentSiteExists(){

  var deferred = jQuery.Deferred();

  //grab all sites
  websites = getWebsites();
  jQuery.when(websites).done(function(data){
      exists = false;
      //loop through each website and see if it matches
      jQuery.each(data, function(index, val) {

        //disregard http vs https, they are treated as same site
        url = val.url.split("//").pop();

        //see if it matches current hostname url
         if(url == window.location.hostname){
           exists = val;
         }
      })

      deferred.resolve(exists);

  })

  return deferred.promise();

}

/**
 * validates an auth token and grabs user data from it
 * @param  {[str]} token [description]
 * @return {[type]}          [description]
 */
function validateAuthToken(token){

  var deferred = jQuery.Deferred();

  jQuery.ajax({
  url : at_api+"/validate/auth_token/"+token,
  dataType: 'json',
  success: function(data, textStatus, jqXHR)
      {
        console.log("success");
        console.log(data)
        deferred.resolve(data);
      },
      error: function (jqXHR, textStatus, errorThrown)
      {
        console.log("error");
        console.log(errorThrown);
      }
    });

  return deferred.promise();

}

/**
 * Function that will redirect to a new page & pass data using submit
 * @param {type} path -> new url
 * @param {type} params -> JSON data to be posted
 * @param {type} method -> GET or POST
 * @returns {undefined} -> NA
 */
function gotoUrl(path, params, method) {
    //Null check
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    //Fill the hidden form
    if (typeof params === 'string') {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", 'data');
        hiddenField.setAttribute("value", params);
        form.appendChild(hiddenField);
    }
    else {
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                if(typeof params[key] === 'object'){
                    hiddenField.setAttribute("value", JSON.stringify(params[key]));
                }
                else{
                    hiddenField.setAttribute("value", params[key]);
                }
                form.appendChild(hiddenField);
            }
        }
    }

    document.body.appendChild(form);
    form.submit();
}
