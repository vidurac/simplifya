/**
 * Created by Nishan on 5/9/2016.
 */

// Notification Success
function NotifySuccess(title, message) {
    $.growl.notice({title: title, message: message, location: "br"});
}

// Notification Success
function NotifySuccessLarge(title, message) {
    $.growl.notice({title: title, message: message, size: 'large', location: "br"});
}

// Notification Error
function NotifyError(title,message){
    $.growl.error({ title:title, message: message ,location:"br"});
}