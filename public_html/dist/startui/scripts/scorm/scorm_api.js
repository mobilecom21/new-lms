alert = function() {};
function LMSInitialize (param) {
    return true;
}
function LMSFinish (param) {
    return true;
}
function LMSGetValue (param) {
    return true;
}
function LMSSetValue (param) {
    return true;
}
function LMSCommit (param) {
    return true;
}
function LMSGetLastError (param) {
    return true;
}
function LMSGetErrorString (param) {
    return true;
}
function LMSGetDiagnostic (param) {
    return true;
}
var API = {};
API.LMSInitialize = LMSInitialize;
API.LMSFinish = LMSFinish;
API.LMSGetValue = LMSGetValue;
API.LMSSetValue = LMSSetValue;
API.LMSCommit = LMSCommit;
API.LMSGetLastError = LMSGetLastError;
API.LMSGetErrorString = LMSGetErrorString;
API.LMSGetDiagnostic = LMSGetDiagnostic;