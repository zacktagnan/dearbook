const isImage = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split("/");
    return mime[0].toLowerCase() === "image";
};

const isVideo = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split("/");
    return mime[0].toLowerCase() === "video";
};

export { isImage, isVideo, }
