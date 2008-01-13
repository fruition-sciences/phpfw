alert('js');
function button_click(url) {
  url = normalizeUrl(url);
  document.location = url;
}

function button_submit(url) {
  var form = document.getElementById("theForm");
  form.method = "post";
  url = normalizeUrl(url);
  form.action = url;
  form.submit();
}

function normalizeUrl(url) {
  if (url.charAt(0) == '/') {
    return appBase + url.substring(1);
  }
  else return url;
}