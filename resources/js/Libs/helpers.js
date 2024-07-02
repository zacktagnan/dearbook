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

const reactionTypesFormat = {
    'all': {
        'main_type_button_text': 'Like',
        'classes': '',
        'reaction_modal_header_text': 'Todas',
        'icon': 'Sin',
    },
    'like': {
        'main_type_button_text': 'Like',
        'classes': 'text-sky-500',
        'reaction_modal_header_text': 'Like',
        'icon': 'Sin',
    },
    'love': {
        'main_type_button_text': 'Love',
        'classes': 'text-rose-500',
        'reaction_modal_header_text': 'Love',
        'icon': 'Sin',
    },
    'care': {
        'main_type_button_text': 'Care',
        'classes': 'text-yellow-500',
        'reaction_modal_header_text': 'Care',
        'icon': 'Sin',
    },
    'haha': {
        'main_type_button_text': 'Haha',
        'classes': 'text-yellow-500',
        'reaction_modal_header_text': 'Haha',
        'icon': 'Sin',
    },
    'wow': {
        'main_type_button_text': 'Wow',
        'classes': 'text-yellow-500',
        'reaction_modal_header_text': 'Wow',
        'icon': 'Sin',
    },
    'sad': {
        'main_type_button_text': 'Sad',
        'classes': 'text-blue-300',
        'reaction_modal_header_text': 'Sad',
        'icon': 'Sin',
    },
    'angry': {
        'main_type_button_text': 'Angry',
        'classes': 'text-orange-600',
        'reaction_modal_header_text': 'Angry',
        'icon': 'Sin',
    },
};

export { isImage, isVideo, reactionTypesFormat, }
