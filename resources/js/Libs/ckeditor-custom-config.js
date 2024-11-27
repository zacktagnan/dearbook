
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import "@ckeditor/ckeditor5-build-classic/build/translations/es";

const editor = ClassicEditor;
const editorConfig = {
    language: "es",
    mediaEmbed: {
        removeProviders: [
            'dailymotion', 'flickr', 'facebook', 'googleMaps', 'instagram',
            'spotify', 'twitter', 'vimeo', 'youtube',
        ],
    },
    toolbar: {
        items: [
            "undo",
            "redo",
            "|",
            "heading",
            "|",
            "bold",
            "italic",
            "|",
            "link",
            "bulletedList",
            "numberedList",
            "|",
            "outdent",
            "indent",
            "blockquote",
        ],
    },
    link: {
        decorators: {
            openInNewTab: {
                mode: "manual",
                label: "Open in a new tab",
                attributes: {
                    target: "_blank",
                    rel: "noopener noreferrer",
                },
            },
        },
    },
};

export { editor, editorConfig, }
