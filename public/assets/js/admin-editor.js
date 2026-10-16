document.addEventListener('DOMContentLoaded', () => {
  if (!window.Jodit) {
    return;
  }

  const buttons = [
    'undo', 'redo', '|',
    'paragraph', '|',
    'bold', 'italic', 'underline', 'strikethrough', 'eraser', '|',
    'ul', 'ol', 'outdent', 'indent', '|',
    'link', 'table', 'hr', '|',
    'find', 'source', 'fullsize',
  ];

  document.querySelectorAll('textarea[data-editor]').forEach((textarea) => {
    window.Jodit.make(textarea, {
      language: 'en',
      height: 460,
      minHeight: 240,
      placeholder: 'Start writing…',
      toolbarAdaptive: false,
      toolbarButtonSize: 'middle',
      buttons,
      controls: {
        paragraph: {
          list: window.Jodit.atom({
            p: 'Paragraph',
            h2: 'Heading',
            h3: 'Subheading',
            h4: 'Small heading',
            blockquote: 'Quote',
          }),
        },
      },
      askBeforePasteHTML: false,
      askBeforePasteFromWord: false,
      defaultActionOnPaste: 'insert_clear_html',
      defaultActionOnPasteFromWord: 'insert_clear_html',
      cleanHTML: {
        denyTags: 'script,style,iframe,object,embed,form,input,button,select,textarea,link,meta',
      },
      disablePlugins: ['speech-recognize', 'ai-assistant', 'about', 'print', 'image', 'video', 'file', 'media'],
      sourceEditor: 'area',
      beautifyHTML: false,
      showCharsCounter: true,
      showWordsCounter: true,
      showXPathInStatusbar: false,
      hidePoweredByJodit: true,
    });
  });
});
