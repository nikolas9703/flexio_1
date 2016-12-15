var config = {};
//Toolbar groups configuration.
config.toolbarGroups = [
    '/',
    { name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
    { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'styles', groups: [ 'styles' ] },
    { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
    { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
    { name: 'forms', groups: [ 'forms' ] },
    '/',
    { name: 'links', groups: [ 'links' ] },
    { name: 'insert', groups: [ 'insert' ] },
    '/',
    { name: 'colors', groups: [ 'colors' ] },
    { name: 'tools', groups: [ 'tools' ] },
    //{ name: 'others', groups: [ 'others' ] },
    //{ name: 'about', groups: [ 'about'] }
];
config.removeButtons = 'Image,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,BidiRtl,Language,Unlink,Anchor,RemoveFormat,Form,Checkbox,TextField,Radio,Button,ImageButton,Textarea,HiddenField,Scayt,BidiLtr,Blockquote,CreateDiv,Outdent,Indent,Maximize,ShowBlocks,About,BGColor,TextColor,Link,Font,Select,SelectAll,Find,Replace,Cut,Copy,Paste,PasteText,PasteFromWord,Templates,Preview,NewPage,Save,Source,Superscript,Subscript,Styles,Format,CreatePlaceholder,PlaceholderElements,Print';
config.height = 400;
config.resize_enaled = false;
config.removeDialogTabs = 'link:advanced';
config.extraPlugins = 'placeholder_elements';
config.entities = false;
config.basicEntities = true;
config.entities_greek = false;
config.entities_latin = false;
config.htmlEncodeOutput = true;
config.entities_additional = '';
config.placeholder_elements = {};