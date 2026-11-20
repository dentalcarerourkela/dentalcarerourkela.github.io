/**
 * Visitor counter backend — paste into script.google.com (Code.gs) and deploy as a Web app.
 * GET ?hit=1  -> adds 1 and returns {"count": N}
 * GET ?hit=0  -> just returns {"count": N}
 * To start from a higher number, run setStart() once after editing the number below.
 */
function doGet(e) {
  var lock = LockService.getScriptLock();
  lock.waitLock(10000);
  try {
    var props = PropertiesService.getScriptProperties();
    var n = parseInt(props.getProperty('count') || '0', 10);
    if (e && e.parameter && e.parameter.hit === '1') {
      n++;
      props.setProperty('count', String(n));
    }
    return ContentService
      .createTextOutput(JSON.stringify({ count: n }))
      .setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}

function setStart() {
  PropertiesService.getScriptProperties().setProperty('count', '0'); // change 0 to your starting number
}
