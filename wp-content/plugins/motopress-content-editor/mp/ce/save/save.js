steal("jquery/class",function($){$.Class("CE.Save",{isContentChanged:false,excerpt:"",isNeedSaveExcerpt:false,save:function(){this.isContentChanged=false;this.convertAndInsert();this.isNeedSaveExcerpt=false;this.excerpt=""},convertAndInsert:function(){var dom=CE.Iframe.contents.find("body .motopress-content-wrapper");if(dom.length){var src="";src=this.getSources(src,dom);if(parent.CE.Iframe.wpEditor!==null){parent.CE.Iframe.wpEditor.setContent(src,{format:"html"})}else{parent.CE.Iframe.wpTextarea.val(parent.switchEditors._wp_Nop(src))}if(parent.MP.Settings.saveExcerpt&&this.isNeedSaveExcerpt){var excerptEl=$("#postexcerpt #excerpt",parent.document);if(excerptEl.length){excerptEl.val(parent.switchEditors._wp_Nop(this.excerpt))}}}},getSources:function(src,dom,level){if(typeof level==="undefined"){level=1}var $this=this;var mpRow=null;var mpSpan=null;if(level===1){mpRow=CE.Iframe.window.CE.LeftBar.myThis.library.mp_grid.objects.mp_row.id;mpSpan=CE.Iframe.window.CE.LeftBar.myThis.library.mp_grid.objects.mp_span.id}else{mpRow=CE.Iframe.window.CE.LeftBar.myThis.library.mp_grid.objects.mp_row_inner.id;mpSpan=CE.Iframe.window.CE.LeftBar.myThis.library.mp_grid.objects.mp_span_inner.id}dom.children(".mp-row-fluid.motopress-row, .motopress-more-handler").each(function(){if(level===1&&$(this).hasClass("motopress-more-handler")){if(!$(this).hasClass("motopress-more-handler-disabled")){$this.isNeedSaveExcerpt=true;$this.excerpt=src;src+="<!--more-->"}}else{src+="<p>["+mpRow+$this.getAttributes($(this),"parameters")+$this.getAttributes($(this),"styles")+"]</p>";$(this).children('[class*="span"].motopress-span').each(function(){var spanClass=MP.Utils.getSpanClass($(this).prop("class").split(" "));var col=MP.Utils.getSpanNumber(spanClass);var classes="";var style="";if($(this).hasClass("motopress-empty")){classes=' classes="motopress-empty mp-hidden-phone"'}if($(this).hasClass("motopress-space")){classes=' classes="motopress-space"'}var minHeight=parseInt($(this).css("min-height"));if(minHeight!==CE.Iframe.window.CE.Resizer.myThis.minHeight&&minHeight!==CE.Iframe.window.CE.Resizer.myThis.spaceMinHeight){style=' style="min-height: '+$(this).css("min-height")+';"'}src+="<p>["+mpSpan+' col="'+col+'"'+classes+style+$this.getAttributes($(this),"parameters")+$this.getAttributes($(this),"styles")+"]</p>";if($(this).children(".mp-row-fluid.motopress-row").length){src=$this.getSources(src,$(this),level+1)}else{src=$this.getShortcode(src,$(this).find(".motopress-block-content > [data-motopress-shortcode]"))}src+="<p>[/"+mpSpan+"]</p>"});src+="<p>[/"+mpRow+"]</p>"}});return src},getShortcode:function(src,shortcode){var child=shortcode.children("div");if(shortcode.length){var shortcodeSrc="";var name=shortcode.attr("data-motopress-shortcode");var closeType=shortcode.attr("data-motopress-close-type");var unwrap=(typeof shortcode.attr("data-motopress-unwrap")==="undefined")?false:true;var start="";var end="";if(closeType==="enclosed"){var content=shortcode.attr("data-motopress-content");content=(!content)?"":content.replace(/\[\]/g,"[");shortcodeSrc+=content;end="<p>[/"+name+"]</p>"}if(!unwrap){start="<p>["+name+this.getAttributes(shortcode,"parameters")+this.getAttributes(shortcode,"styles")+"]</p>";shortcodeSrc=start+shortcodeSrc+end}src+=shortcodeSrc}return src},changeContent:function(){if(!this.isContentChanged){var wpEditor=parent.CE.Iframe.wpEditor;if(wpEditor){var contentSrc=wpEditor.getContent({format:"html"});contentSrc+="MOTOPRESS_"+MP.Utils.uniqid();wpEditor.setContent(contentSrc,{format:"html"});wpEditor.focus();this.isContentChanged=true}}},getAttributes:function(obj,attribute){var attributes=obj.attr("data-motopress-"+attribute);var result="";if(attributes){attributes=JSON.parse(attributes);$.each(attributes,function(key,attrs){if(typeof attrs.value!=="undefined"&&attrs.value!==""){result+=" "+key+'="'+attrs.value+'"'}})}return result}},{})});