// =============================================
// Casa De Manila — IP Spam Guard
// spam_guard.gs
// =============================================

const MAX_ATTEMPTS  = 20;
const BLOCK_MINUTES = 30;

function checkSpam(userIP) {
  if (!userIP || userIP === "Unknown") {
    return { blocked: false };
  }

  const props    = PropertiesService.getScriptProperties();
  const safeKey  = userIP.replace(/[^a-zA-Z0-9_]/g, "_");
  const countKey = "count_" + safeKey;
  const timeKey  = "time_"  + safeKey;
  const now      = new Date().getTime();

  const rawCount = props.getProperty(countKey);
  const rawTime  = props.getProperty(timeKey);

  const attempts  = rawCount ? parseInt(rawCount) : 0;
  const firstSeen = rawTime  ? parseInt(rawTime)  : now;

  const minutesElapsed = (now - firstSeen) / 1000 / 60;

  // Block window expired — reset
  if (minutesElapsed >= BLOCK_MINUTES) {
    props.deleteProperty(countKey);
    props.deleteProperty(timeKey);
    props.setProperty(countKey, "1");
    props.setProperty(timeKey, String(now));
    return { blocked: false };
  }

  // Over the limit — block
  if (attempts >= MAX_ATTEMPTS) {
    const minutesLeft = Math.ceil(BLOCK_MINUTES - minutesElapsed);
    Logger.log("Blocked IP: " + userIP + " | Attempts: " + attempts);
    return {
      blocked: true,
      message: `Too many reservation attempts. Please try again in ${minutesLeft} minute(s).`
    };
  }

  // Increment counter
  props.setProperty(countKey, String(attempts + 1));
  if (!rawTime) props.setProperty(timeKey, String(now));

  Logger.log("IP: " + userIP + " | Attempt: " + (attempts + 1) + "/" + MAX_ATTEMPTS);
  return { blocked: false };
}