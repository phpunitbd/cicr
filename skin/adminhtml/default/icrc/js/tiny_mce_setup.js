if (typeof(tinyMceWysiwygSetup) != "undefined") {
  tinyMceWysiwygSetup.prototype.initialize = function(htmlId, config)
  {
    this.id = htmlId;
    this.config = config;
    this.config.content_css = "/skin/adminhtml/default/icrc/css/tiny_mce/icrc_cms.css";
    varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));
    this.notifyFirebug();
    if(typeof tinyMceEditors == 'undefined') {
      tinyMceEditors = $H({});
    }
    tinyMceEditors.set(this.id, this);
  }
}

