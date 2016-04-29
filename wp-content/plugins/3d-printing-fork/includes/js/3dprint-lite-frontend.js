/**
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */
p3dlite.bar_progress=0;
p3dlite.xhr='';
p3dlite.schedule_quote=0;
p3dlite.filereader_supported=true;
p3dlite.file_selected=0;
p3dlite.aabb = new Array();
window.onload = function() {

	window.p3dlite_canvas = document.getElementById('p3dlite-cv');

	jQuery("#p3dlite-file-loading").css({
		top: jQuery("#p3dlite-cv").position().top+jQuery("#p3dlite-cv").height()/2-jQuery("#p3dlite-file-loading").height()/2,
		left: jQuery("#p3dlite-cv").position().left + jQuery("#p3dlite-cv").width()/2-jQuery("#p3dlite-file-loading").width()/2
	}) ;
	jQuery("#canvas-stats").css({
		top: jQuery("#p3dlite-cv").position().top ,
		left: jQuery("#p3dlite-cv").position().left
	}) ;
	var logoTimerID = 0;

	window.p3dlite_viewer = new JSC3D.Viewer(window.p3dlite_canvas);
	p3dlite_viewer.setParameter('InitRotationX', p3dlite.angle_x);
	p3dlite_viewer.setParameter('InitRotationY', p3dlite.angle_y);
	p3dlite_viewer.setParameter('InitRotationZ', p3dlite.angle_z);
	p3dlite_viewer.setParameter('BackgroundColor1', p3dlite.background1);
	p3dlite_viewer.setParameter('BackgroundColor2', p3dlite.background2);
	p3dlite_viewer.setParameter('RenderMode', 'textureflat');
	p3dlite_viewer.setParameter('ProgressBar', 'off');
	p3dlite_viewer.setParameter('Renderer', 'webgl'); 
	window.wp.hooks.doAction( '3dprint-lite.p3dlite_viewerConfig');

	p3dlite_viewer.onloadingstarted = function() {
		p3dliteDisplayUserDefinedProgressBar(true);
	};
	p3dlite_viewer.onloadingcomplete = p3dlite_viewer.onloadingaborted = p3dlite_viewer.onloadingerror = function() {
		p3dliteDisplayUserDefinedProgressBar(false);
		if(logoTimerID > 0) return;
		// show statistics of current model when loading is completed
		var scene = p3dlite_viewer.getScene();
		if(scene && scene.getChildren().length > 0) {
			var objects = scene.getChildren();
			var totalFaceCount = 0;
			var totalVertexCount = 0
			for(var i=0; i<objects.length; i++) {
				totalFaceCount += objects[i].faceCount;
				totalVertexCount += objects[i].vertexBuffer.length / 3;
			}
			var stats = totalVertexCount.toString() + ' vertices' + '<br/>' + totalFaceCount.toString() + ' faces';
			document.getElementById('p3dlite-statistics').innerHTML = stats;
			if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('color'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('color').length>0 ) {
				p3dliteChangeModelColor(jQuery('input[name=product_coating]:checked').closest('li').data('color'));
			}
			else {
				p3dliteChangeModelColor(jQuery('input[name=product_filament]:checked').closest('li').data('color'));
			}

			printer_id=jQuery('input[name=product_printer]:checked').data('id')
	                p3dliteMakeGroundPlane();
			p3dliteDrawPrinterBox(scene, printer_id, jQuery('input[name=p3dlite_unit]:checked').val());
			// ask the p3dlite_viewer to apply this change immediately
			p3dlite_viewer.resetScene();
			p3dlite_viewer.zoomFactor=parseInt(p3dlite.zoom);
			p3dlite_viewer.update();
			p3dliteGetStats();
		}
		else {
			document.getElementById('p3dlite-statistics').innerHTML = '';
		}
	window.wp.hooks.doAction( '3dprint-lite.modelLoaded');
	};



	if (jQuery('input[name=get_printer_id]').val())	{
		printer=jQuery('input[name=get_printer_id]').val()
		jQuery.cookie('p3dlite_printer', printer);
	}
	else if (jQuery.cookie('p3dlite_printer')!=='undefined' && jQuery('#p3dlite_printer_'+jQuery.cookie('p3dlite_printer')).length>0) {
		printer=jQuery.cookie('p3dlite_printer');
	}
	else {
		printer=jQuery('input[name=product_printer]').data('id');

	}

	if (jQuery('input[name=get_material_id]').val()) {
		material=jQuery('input[name=get_material_id]').val()
		jQuery.cookie('p3dlite_material', material);
	}
	else if (jQuery.cookie('p3dlite_material')!=='undefined' && jQuery('#p3dlite_material_'+jQuery.cookie('p3dlite_material')).length>0)	{
		material=jQuery.cookie('p3dlite_material');
	}
	else {
		material=jQuery('input[name=product_filament]').data('id');
	}
	if (jQuery('input[name=get_coating_id]').val()) {
		coating=jQuery('input[name=get_coating_id]').val()
		jQuery.cookie('p3dlite_coating', coating);
	}
	else if (jQuery.cookie('p3dlite_coating')!='undefined' && jQuery('#p3dlite_coating_'+jQuery.cookie('p3dlite_coating')).length>0)	{
		coating=jQuery.cookie('p3dlite_coating');
	}
	else {
		coating=jQuery('input[name=product_coating]').data('id');
	}

	if (jQuery('input[name=get_product_model]').val()) {
		product_file=jQuery('input[name=get_product_model]').val();
		jQuery.cookie('p3dlite_file', product_file);
	}
	else {
		product_file=jQuery.cookie('p3dlite_file');
	}

	if (jQuery('input[name=get_product_unit]').val()) {
		product_unit=jQuery('input[name=get_product_unit]').val();
		jQuery.cookie('p3dlite_unit', product_unit);
	}
	else if (jQuery.cookie('p3dlite_unit')!=='undefined') {
		product_unit=jQuery.cookie('p3dlite_unit');
	}
	else {
		product_unit='mm';
	}

	material_volume=jQuery.cookie('p3dlite-stats-material-volume');

	jQuery('#stats-material-volume').html(jQuery.cookie('p3dlite-stats-material-volume'));

	jQuery('#stats-box-volume').html(jQuery.cookie('p3dlite-stats-box-volume'));
	jQuery('#stats-width').html(jQuery.cookie('p3dlite-stats-width'));
	jQuery('#stats-length').html(jQuery.cookie('p3dlite-stats-length'));
	jQuery('#stats-height').html(jQuery.cookie('p3dlite-stats-height'));

	jQuery('#stats-weight').html(jQuery.cookie('p3dlite-stats-weight'));
	if (jQuery.cookie('p3dlite-stats-material-volume')) jQuery('.p3dlite-stats').show();

	if (typeof(printer)!=='undefined') {
		jQuery('#p3dlite_printer_'+printer).attr('checked', 'checked');
		p3dliteSelectPrinter(jQuery('#p3dlite_printer_'+printer).closest('li'));
	}
	else {
		jQuery('#p3dlite_printer_0').attr('checked', 'checked');
		p3dliteSelectPrinter(jQuery('#p3dlite_printer_0').closest('li'));
	}

	if (typeof(material)!=='undefined') {
		jQuery('#p3dlite_material_'+material).attr('checked', 'checked');
		p3dliteSelectFilament(jQuery('#p3dlite_material_'+material).closest('li'));
	}
	else {
		jQuery('#p3dlite_material_0').attr('checked', 'checked');
		p3dliteSelectFilament(jQuery('#p3dlite_material_0').closest('li'));
	}

	if (typeof(coating)!='undefined') {
		jQuery('#p3dlite_coating_'+coating).attr('checked', 'checked');
		p3dliteSelectCoating(jQuery('#p3dlite_coating_'+coating).closest('li'));
	}
	else if (jQuery('#p3dlite_coating_0').length>0) {
		jQuery('#p3dlite_coating_0').attr('checked', 'checked');
		p3dliteSelectCoating(jQuery('#p3dlite_coating_0').closest('li'));
	}


	if (typeof(product_file)!=='undefined') {
		jQuery('#pa_p3dlite_model').val(product_file);
		p3dlite_viewer.setParameter('SceneUrl', p3dlite.upload_url+product_file);
	}
	if (typeof(product_unit)!=='undefined') {
		jQuery("input[name=p3dlite_unit][value=" + product_unit + "]").attr('checked', 'checked');
		p3dliteSelectUnit(jQuery("input[name=p3dlite_unit][value=" + product_unit + "]"));
	}
	else {
		p3dliteSelectUnit(jQuery("input[name=p3dlite_unit][value=mm]"));
	}

	if (typeof(printer)!=='undefined' && typeof(material)!=='undefined' && typeof(product_file)!=='undefined') {
		p3dliteGetStats();
	}
	else {
		jQuery('#p3dlite-file-loading').hide();
		jQuery('#p3dlite-quote-loading').css('visibility', 'hidden');
	}
	p3dlite_viewer.init();
	p3dlite_viewer.update();
}

jQuery(document).ready(function(){
window.p3dlite_uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,browserplus,gears,html4',
	browse_button : 'p3dlite-pickfiles', // you can pass an id...
	multi_selection : false,
	multiple_queues : false,
	max_file_count : 1,
	max_file_size: p3dlite.file_max_size+"mb",
	container: document.getElementById('p3dlite-container'), 
	url : p3dlite.url,
	chunk_size : '2mb',
	flash_swf_url : p3dlite.plugin_url+'includes/ext/plupload/Moxie.swf',
	silverlight_xap_url : p3dlite.plugin_url+'includes/ext/plupload/Moxie.xap',
	filters : {
	mime_types: [
		{
			title : p3dlite.file_extensions+" files", 
			extensions : p3dlite.file_extensions
		}
	]
	},
	init: {
		QueueChanged: function(p3dlite_uploader) {
			if(p3dlite_uploader.files.length > 1) {
				jQuery('#p3dlite-filelist').html('');
				p3dlite_uploader.files.splice(0, 1);
			}
		},
		PostInit: function() {
			document.getElementById('p3dlite-filelist').innerHTML = '';
			document.getElementById('p3dlite-console').innerHTML = '';

		},
		Browse: function() {
		},
		FilesAdded: function(up, files) {
			p3dlite.bar_progress=0;
			jQuery('.p3dlite-mail-success').hide();
			jQuery('.p3dlite-mail-error').hide();
			window.wp.hooks.doAction( '3dprint-lite.filesAdded');
			if (p3dlite.filereader_supported)
			{
				var file = files[0].getNative();
				var file_ext = file.name.split('.').pop().toLowerCase();
				if (file_ext != 'zip') {
				p3dlite.filereader_supported = true;
				var reader = new FileReader();
				reader.onload = function(event) {
					var chars  = new Uint8Array(event.target.result);
					var CHUNK_SIZE = 0x8000; 
					var index = 0;
					var length = chars.length;
					var result = '';
					var slice;
					while (index < length) {
						slice = chars.subarray(index, Math.min(index + CHUNK_SIZE, length)); 
						result += String.fromCharCode.apply(null, slice);
						index += CHUNK_SIZE;
					}

					theScene = new JSC3D.Scene

					if (file_ext=='stl') {
						stl_loader = new JSC3D.StlLoader()
						stl_loader.parseStl(theScene, result)
					}
					else if (file_ext=='obj') {
						obj_loader = new JSC3D.ObjLoader()
						obj_loader.parseObj(theScene, result)
					}
					else alert('Unsupported format');

					jQuery('#p3dlite-file-loading').hide();

	                		p3dlite_viewer.replaceSceneFromUrl("");//hack to empty the sceneUrl
			                p3dlite_viewer.init()
			                p3dlite_viewer.replaceScene(theScene)

					if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('color'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('color').length>0 ) {
						p3dliteChangeModelColor(jQuery('input[name=product_coating]:checked').closest('li').data('color'));
					}
					else {
						p3dliteChangeModelColor(jQuery('input[name=product_filament]:checked').closest('li').data('color'));
					}

			                p3dliteMakeGroundPlane();
			                p3dliteDrawPrinterBox(p3dlite_viewer.getScene(), jQuery('input[name=product_printer]:checked').data('id'), jQuery('input[name=p3dlite_unit]:checked').val());
			                p3dlite_viewer.resetScene();
					p3dlite_viewer.zoomFactor=parseInt(p3dlite.zoom);
			                p3dlite_viewer.update()
					p3dliteGetStats();
			                var scene = p3dlite_viewer.getScene();
			                var objects = scene.getChildren();
			                var totalFaceCount = 0;
			                var totalVertexCount = 0
			                for(var i=0; i<objects.length; i++) {
			                	totalFaceCount += objects[i].faceCount;
			                	totalVertexCount += objects[i].vertexBuffer.length / 3;
                			}
			                var stats = totalVertexCount.toString() + ' vertices' + '<br/>' + totalFaceCount.toString() + ' faces';
			                document.getElementById('p3dlite-statistics').innerHTML = stats;


            			}
            
				reader.readAsArrayBuffer(file);
				} //!zip
					else p3dlite.filereader_supported = false; //zip file
        		}
		        plupload.each(files, function(file) {
		        	document.getElementById('p3dlite-filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
		        });
		        p3dlite_uploader.disableBrowse(true);
//		        jQuery('.p3dlite-stats').hide();
		        jQuery('#price-container').css('visibility','hidden');
		        jQuery('#add-cart-container').css('visibility','hidden');
		        jQuery('#p3dlite-console').hide();
		        jQuery('#p3dlite-file-loading').show();
		        jQuery('#p3dlite-quote-loading').css('visibility', 'visible');
		        up.start();
		        jQuery('#p3dlite-pickfiles').click();
		},



		UploadProgress: function(up, file) {
			p3dlite.bar_progress=parseFloat(file.percent/100);
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
		},

		UploadComplete: function(up, file, response) {
			p3dlite_uploader.disableBrowse(false);
		},

		Error: function(up, err) {
			p3dlite_uploader.disableBrowse(false);
			document.getElementById('p3dlite-console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
			window.p3dliteProgressButton._stop();
			jQuery('#p3dlite-console').show();
		}
	}
});

p3dlite_uploader.bind('BeforeUpload', function (up, file) {
	up.settings.multipart_params = {
		"action" : 'p3dlite_handle_upload',
		"product_id" : jQuery('#p3dlite_product_id').val(),
		"printer_id" : jQuery('input[name=product_printer]:checked').data('id'),
		"material_id" : jQuery('input[name=product_filament]:checked').data('id'),
		"coating_id" : jQuery('input[name=product_coating]:checked').data('id'),
		"unit" : jQuery('input[name=p3dlite_unit]:checked').val() }
	window.wp.hooks.doAction( '3dprint-lite.beforeUpload');
	});

p3dlite_uploader.init();
p3dlite_uploader.bind('FileUploaded', function(p3dlite_uploader,file,response) {
	var data = jQuery.parseJSON( response.response );
	if (data.error) { //fatal error
		jQuery('#p3dlite-console').html(data.error.message).show();
		jQuery('#p3dlite-file-loading').hide()
		jQuery('#p3dlite-quote-loading').css('visibility', 'hidden');
		return false;
  	}

	jQuery('#p3dlite-quote-loading').css('visibility', 'hidden');
	jQuery('.p3dlite-mail-success').remove();
	jQuery('.p3dlite-mail-error').remove();
        jQuery('#add-cart-container').css('visibility','visible');
        jQuery.cookie('p3dlite_file',data.filename, { expires: 2 });
	if (!p3dlite.filereader_supported) p3dlite_viewer.replaceSceneFromUrl(p3dlite.upload_url+data.filename);
	jQuery.cookie('p3dlite_file',data.filename, { expires: 2 });
	product_file=data.filename;
	jQuery('#pa_p3dlite_model').val(product_file);
	jQuery('.p3dlite-stats').show();
	p3dlite_viewer.update();
	p3dliteCheckPrintability();
	p3dliteGetStats();
	window.wp.hooks.doAction( '3dprint-lite.fileUploaded');
});

});
function p3dliteBoxFitsBox (dim_x1, dim_y1, dim_z1, dim_x2, dim_y2, dim_z2) {
	var fits=true;
	var min_dim1=Math.min(dim_x1, dim_y1, dim_z1);
	var min_dim2=Math.min(dim_x2, dim_y2, dim_z2);
	var max_dim1=Math.max(dim_x1, dim_y1, dim_z1);
	var max_dim2=Math.max(dim_x2, dim_y2, dim_z2);
	var diag1=Math.sqrt(dim_x1 + dim_y1 + dim_z1);
	var diag2=Math.sqrt(dim_x2 + dim_y2 + dim_z2);

	if (min_dim1<min_dim2 && max_dim1<max_dim2 && diag1<diag2) 
		fits = true;
	else 
		fits = false;
	fits=window.wp.hooks.applyFilters('3dprint-lite.boxFitsBox', fits, dim_x1, dim_y1, dim_z1, dim_x2, dim_y2, dim_z2);
	return fits;
}

function p3dliteBoxFitsBoxXY (dim_x1, dim_y1, dim_x2, dim_y2) {
	var fits=true;
	var min_dim1=Math.min(dim_x1, dim_y1);
	var min_dim2=Math.min(dim_x2, dim_y2);
	var max_dim1=Math.max(dim_x1, dim_y1);
	var max_dim2=Math.max(dim_x2, dim_y2);
	var diag1=Math.sqrt(dim_x1 + dim_y1);
	var diag2=Math.sqrt(dim_x2 + dim_y2);

	if (min_dim1<min_dim2 && max_dim1<max_dim2 && diag1<diag2) 
		fits = true;
	else 
		fits = false;
	fits=window.wp.hooks.applyFilters('3dprint-lite.boxFitsBoxXY', fits, dim_x1, dim_y1, dim_x2, dim_y2);
	return fits;
}

function p3dliteShowError(message) {
	var decoded = jQuery('#p3dlite-console').html(message).text();
	jQuery('#p3dlite-console').html(decoded).show();
	window.wp.hooks.doAction( '3dprint-lite.showError');
}

function p3dliteInitProgressButton () {
	if (!p3dliteDetectIE()) {
		window.p3dliteProgressButton=new ProgressButton(document.getElementById('p3dlite-pickfiles'), {
			callback : function( instance ) {
				interval = setInterval( function() {
					instance._setProgress( p3dlite.bar_progress );
					if( parseInt(p3dlite.bar_progress) === 1 ) {
						instance._stop(1);
						clearInterval( interval );
					}
				}, 200 );
			}
		} );
	}
}

jQuery(document).ready(function() {
	p3dliteInitProgressButton();
});

function p3dliteChangeModelColor(color) {
	if (p3dlite_viewer.getScene()==null) return false;
	var objects = p3dlite_viewer.getScene().getChildren();
	for(var i=0; i<objects.length; i++) {
		if ( objects[i].name!='printerbox' && objects[i].name!='groundplane' && (typeof(objects[i].mtl)=='undefined' || objects[i].mtl=='')) {
			objects[i].setMaterial(new JSC3D.Material('', 0, color.replace('#','0x'), 0, true));		
		}
	}
	p3dlite_viewer.update();

};

function p3dliteSelectFilament(obj) {
	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('#pa_p3dlite_material').val(jQuery(obj).find('input').data('id'));
	material_id=jQuery(obj).find('input').data('id');
	if (typeof(jQuery('input[name=product_coating]:checked').closest('li').data('color'))!=='undefined' && jQuery('input[name=product_coating]:checked').closest('li').data('color').length>0 )
		p3dliteChangeModelColor(jQuery('input[name=product_coating]:checked').closest('li').data('color'));
	else
		p3dliteChangeModelColor(jQuery(obj).attr('data-color'));

	jQuery.cookie('p3dlite_material', jQuery(obj).find('input').attr('data-id'), { expires: 2 });
	if (p3dliteCheckPrintability()) p3dliteGetStats();
	window.wp.hooks.doAction( '3dprint-lite.selectFilament');
}

function p3dliteSelectCoating(obj) {

	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('#pa_p3dlite_coating').val(jQuery(obj).find('input').data('id'));
	coating_id=jQuery(obj).find('input').data('id');

	if (typeof(jQuery(obj).attr('data-color'))!=='undefined' && jQuery(obj).attr('data-color').length>0) {
		p3dliteChangeModelColor(jQuery(obj).attr('data-color'));
	}
	else {
		p3dliteChangeModelColor(jQuery('input[name=product_filament]:checked').closest('li').data('color'));
	}

	jQuery.cookie('p3dlite_coating', jQuery(obj).find('input').attr('data-id'), { expires: 2 });

	p3dliteGetStats();
	window.wp.hooks.doAction( '3dprint.selectCoating');
}


function p3dliteSelectUnit(obj) {
	jQuery(obj).attr('checked','true');
	jQuery('#p3dlite_unit').val(jQuery(obj).val());
	jQuery('#pa_p3dlite_unit').val(jQuery(obj).val());
	product_unit=jQuery(obj).val();
	jQuery.cookie('p3dlite_unit', jQuery(obj).val(), { expires: 2 });
	printer_id=jQuery('input:radio[name=product_printer]:checked').data('id');
	p3dliteChangePrinter(printer_id);
	p3dliteGetStats();
	window.wp.hooks.doAction( '3dprint-lite.selectUnit');
}

function p3dliteChangePrinter(printer_id) {
	if (p3dlite_viewer.getScene()==null) return false;

	var scene = p3dlite_viewer.getScene();
	var objects = scene.getChildren();
	for(var i=0; i<objects.length; i++) {
		if ( objects[i].name=='printerbox' ) {
			scene.removeChild(objects[i]);
		}
	}


	p3dliteDrawPrinterBox(scene, printer_id, jQuery('input[name=p3dlite_unit]:checked').val());
}
function p3dliteSelectPrinter(obj) {
	jQuery(obj).find('input[type=radio]').attr('checked','true');
	jQuery('#pa_p3dlite_printer').val(jQuery(obj).find('input').data('id'));
	jQuery.cookie('p3dlite_printer', jQuery(obj).find('input').attr('data-id'), { expires: 2 });
	printer_id=jQuery(obj).find('input').data('id');
	p3dliteChangePrinter(printer_id);

	if (p3dliteCheckPrintability()) p3dliteGetStats();
	window.wp.hooks.doAction( '3dprint-lite.selectPrinter');
}

function p3dliteCheckPrintability() {

//todo: many things
	var printable=true;
	var x_dim=parseFloat(jQuery('#stats-length').html());
	var y_dim=parseFloat(jQuery('#stats-width').html());
	var z_dim=parseFloat(jQuery('#stats-height').html());
	if (!x_dim || !y_dim || !z_dim) return false;

	var printer_width=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-width'));
	var printer_length=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-length'));
	var printer_height=parseFloat(jQuery('input:radio[name=product_printer]:checked').attr('data-height'));


	if (!p3dliteBoxFitsBox(x_dim*10, y_dim*10, z_dim*10, printer_width, printer_length, printer_height)) {
		p3dliteShowError(p3dlite.error_box_fit);
		printable=false;
	}
	else if (!p3dliteBoxFitsBoxXY(x_dim*10, y_dim*10, printer_width, printer_length)) {
		p3dliteShowError(p3dlite.warning_box_fit);
	}

	if (!printable) { 
		jQuery('#price-container').css('visibility','hidden');
		jQuery('#add-cart-container').css('visibility','hidden');
	}
	else { 
		jQuery('#printer_fit_error').hide();
	}
	printable=window.wp.hooks.applyFilters('3dprint-lite.checkPrintability', printable);
	return printable;
}

function p3dliteCalculatePrintingCost( product_info ) {
	var material = jQuery('input[name=product_filament]:checked');
	var coating = jQuery('input[name=product_coating]:checked');
	var printer = jQuery('input[name=product_printer]:checked');
	var material_cost = 0;
	var coating_cost = 0;
	var printing_cost = 0;
	
	printing_volume=product_info['model']['material_volume'];

	if ( material.data('price_type')=='cm3' ) {
		material_cost=( printing_volume )*material.data('price');
	}
	else if ( material.data('price_type')=='gram' ) {
		material_cost=product_info['model']['weight']*material.data('price');

	}

	if ( printer.data('price_type')=="material_volume" ) {
		printing_cost=printing_volume*printer.data('price');
	}
	else if ( printer.data('price_type')=="box_volume" ) {
		printing_cost=product_info['model']['box_volume']*printer.data('price');
	}
	else if ( printer.data('price_type')=="gram" ) {
		printing_cost=product_info['model']['weight']*printer.data('price');
	}
	if (typeof(coating.data('price'))!=='undefined') {
		coating_cost = product_info['model']['surface_area'] * coating.data('price');
	}

	var total=printing_cost+material_cost+coating_cost;
	if (total < parseFloat(p3dlite.min_price)) total = parseFloat(p3dlite.min_price);
	total=window.wp.hooks.applyFilters('3dprint-lite.calculatePrintingCost', total, product_info);
	return total;
}

//an example hook
window.wp.hooks.addFilter( '3dprint-lite.calculatePrintingCost', function  (total, product_info) {
	//do something with total
	return total;
})

function p3dliteGetStatsClientSide() {
	var scene=window.p3dlite_viewer.getScene();
	scene.calcAABB();
	var aabb=p3dlite.aabb;
	var filament_volume = window.model_total_volume/1000; //cm3
	var surface_area = window.model_surface_area/100; //cm3
	var model_x = Math.abs(aabb.maxX-aabb.minX)/10
	var model_y = Math.abs(aabb.maxY-aabb.minY)/10
	var model_z = Math.abs(aabb.maxZ-aabb.minZ)/10
	var box_volume = model_x*model_y*model_z; 

	if (product_unit=='inch') {
		model_x = model_x*2.54;
		model_y = model_y*2.54;
		model_z = model_z*2.54;
		surface_area=surface_area*6.4516;
		box_volume = model_x*model_y*model_z; 
		filament_volume = filament_volume*16.387064;
	}
	var product_info = new Array();
	product_info['model'] = new Array();
	product_info['model']['x_dim'] = parseFloat(model_x.toFixed(2));
	product_info['model']['y_dim'] = parseFloat(model_y.toFixed(2));
	product_info['model']['z_dim'] = parseFloat(model_z.toFixed(2));
	product_info['model']['material_volume'] = parseFloat(filament_volume.toFixed(2));
	product_info['model']['box_volume'] = parseFloat(box_volume.toFixed(2));
	product_info['model']['surface_area'] = parseFloat(surface_area.toFixed(2));
	product_info['model']['weight'] = parseFloat(filament_volume * parseFloat(jQuery('input[name=product_filament]:checked').data('density')));

	product_info=window.wp.hooks.applyFilters('3dprint-lite.getStatsClientSide', product_info);
	return product_info;
}
function p3dliteGetStats() {
//	jQuery('.p3dlite-stats').hide(); 
	jQuery('#price-container').css('visibility','hidden');
	jQuery('#p3dlite-console').html('').hide();

	var printer_id=jQuery('input:radio[name=product_printer]:checked').attr('data-id');
	var material_id=jQuery('input:radio[name=product_filament]:checked').attr('data-id');
	if (typeof(jQuery('input:radio[name=product_coating]:checked').attr('data-id'))!=='undefined')
		var coating_id=jQuery('input:radio[name=product_coating]:checked').attr('data-id');
	else 
		var coating_id='';
	var product_id=jQuery('#p3dlite_product_id').val();
	var model=jQuery('#pa_p3dlite_model').val();
	var model_unit=jQuery("input[name=p3dlite_unit]:checked").val();

	//it's safe to calculate price on the client when checkout is not allowed

	if (typeof(window.p3dlite_viewer)!=='undefined' && window.p3dlite_viewer.isLoaded)
	{
		var product_info=p3dliteGetStatsClientSide();
		var product_price=p3dliteCalculatePrintingCost(product_info);
		var response = new Array();
		response.model = new Array();
		response.model = product_info['model'];
		response.price = product_price.toFixed(2);

		if (p3dlite.currency_position=='left')
			accounting.settings.currency.format = "%s%v";
		else if (p3dlite.currency_position=='left_space')
			accounting.settings.currency.format = "%s %v";
		else if (p3dlite.currency_position=='right')
			accounting.settings.currency.format = "%v%s";
		else if (p3dlite.currency_position=='right_space')
			accounting.settings.currency.format = "%v %s";

		response.html_price = accounting.formatMoney(response.price, p3dlite.currency_symbol, p3dlite.price_num_decimals, p3dlite.thousand_sep, p3dlite.decimal_sep);

		jQuery('#p3dlite_estimated_price').val(response.price);
		p3dliteShowResponse(response);
	}

	window.wp.hooks.doAction( '3dprint-lite.getStats');
}

function p3dliteShowResponse(response) {
	if (response.error) { //fatal error
		jQuery('#p3dlite-quote-loading').css('visibility', 'hidden');
		p3dliteShowError(response.error);
		return;
	}
	if (window.p3dlite_uploader.state==1) jQuery('#p3dlite-quote-loading').css('visibility','hidden');
	if (response.model) {
		if (response.model.error) p3dliteShowError(response.model.error); //soft error
		jQuery('#stats-material-volume').html(response.model.material_volume.toFixed(2));
		jQuery('#stats-box-volume').html(response.model.box_volume.toFixed(2));
		jQuery('#stats-surface-area').html(response.model.surface_area.toFixed(2));
		jQuery('#stats-width').html(response.model.x_dim.toFixed(2));
		jQuery('#stats-length').html(response.model.y_dim.toFixed(2));
		jQuery('#stats-height').html(response.model.z_dim.toFixed(2));
		jQuery('#stats-weight').html(response.model.weight.toFixed(2));
		jQuery('.p3dlite-stats').show();
	}
	if (p3dliteCheckPrintability()) {
		if (p3dlite.pricing!='request') {
			jQuery('#price-container').css('visibility','visible');
		}
		if (window.p3dlite_uploader.state==1  || !p3dlite.filereader_supported) jQuery('#add-cart-container').css('visibility','visible');
		jQuery('#price-container span.amount').html(response.html_price);
	}
	window.wp.hooks.doAction( '3dprint-lite.showResponse');
}
function p3dliteCalculateWeight(material_volume) {
	var density = parseFloat(jQuery('input[name=product_filament]:checked').attr('data-density'));
	var weight = material_volume*density;
	return weight.toFixed(2);
}


function p3dliteDisplayUserDefinedProgressBar(show) {
	if(show) {
		jQuery('#p3dlite-file-loading').show();
	}
	else {
		jQuery('#p3dlite-file-loading').hide();
	}
}

function p3dliteDetectIE() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
       // IE 12 => return version number
       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
   }

    // other browser
    return false;
}


function p3dliteDrawPrinterBox(scene, printer_id, product_unit) {
	var printer_dim=new Array();
	printer_dim.x=jQuery('#p3dlite_printer_'+printer_id).data('length')
	printer_dim.y=jQuery('#p3dlite_printer_'+printer_id).data('width')
	printer_dim.z=jQuery('#p3dlite_printer_'+printer_id).data('height')

	var scene=window.p3dlite_viewer.getScene();
	scene.calcAABB();
	var sceneBox=p3dlite.aabb;

	var planeCenter = scene.aabb.center();

	var model_xdim=sceneBox.maxX - sceneBox.minX;
	var model_ydim=sceneBox.maxY - sceneBox.minY;
	var model_zdim=sceneBox.maxZ - sceneBox.minZ;

	//xy rotation
	if (model_xdim>printer_dim.x && model_ydim<printer_dim.x) {
		tmpvar=printer_dim.x;
		printer_dim.x=printer_dim.y;
		printer_dim.y=tmpvar;
	}
	else if (model_xdim>printer_dim.x && model_zdim<printer_dim.x) {
		tmpvar=printer_dim.x;
		printer_dim.x=printer_dim.z;
		printer_dim.z=tmpvar;
	}


	if (model_ydim>printer_dim.y && model_xdim<printer_dim.y) {
		tmpvar=printer_dim.y;
		printer_dim.y=printer_dim.x;
		printer_dim.x=tmpvar;
	}
	else if (model_ydim>printer_dim.y && model_zdim<printer_dim.y) {
		tmpvar=printer_dim.y;
		printer_dim.y=printer_dim.z;
		printer_dim.z=tmpvar;
	}

//todo: z-rotation ?

	if (product_unit=='inch') {
		printer_dim.x=printer_dim.x/2.54;
		printer_dim.y=printer_dim.y/2.54;
		printer_dim.z=printer_dim.z/2.54;
	}


	var printerBox = new JSC3D.Mesh('printerbox');
	printerBox.vertexBuffer = [ 
		planeCenter[0] - printer_dim.x/2, planeCenter[1] - printer_dim.y/2, sceneBox.minZ, 
		planeCenter[0] - printer_dim.x/2, planeCenter[1] + printer_dim.y/2, sceneBox.minZ, 
		planeCenter[0] + printer_dim.x/2, planeCenter[1] + printer_dim.y/2, sceneBox.minZ, 
		planeCenter[0] + printer_dim.x/2, planeCenter[1] - printer_dim.y/2, sceneBox.minZ,

		planeCenter[0] - printer_dim.x/2, planeCenter[1] - printer_dim.y/2, sceneBox.minZ + printer_dim.z, 
		planeCenter[0] - printer_dim.x/2, planeCenter[1] + printer_dim.y/2, sceneBox.minZ + printer_dim.z, 
		planeCenter[0] + printer_dim.x/2, planeCenter[1] + printer_dim.y/2, sceneBox.minZ + printer_dim.z, 
		planeCenter[0] + printer_dim.x/2, planeCenter[1] - printer_dim.y/2, sceneBox.minZ + printer_dim.z
	];
	printerBox.indexBuffer = [ 
		0, 1, 2, 3, -1, 
		4, 5, 6, 7, -1, 
		0, 4, 0, 0, -1,
		1, 5, 1, 1, -1,
		2, 6, 2, 2, -1,
		3, 7, 3, 3, -1,
		3, 7, 3, 3, -1
	];
	printerBox.isDoubleSided = true;	
	printerBox.setRenderMode('wireframe');
	printerBox.setMaterial(new JSC3D.Material('plane', 0, p3dlite.printer_color, 0));
	printerBox.init();
	scene.addChild(printerBox);
	scene.calcAABB();
	p3dlite_viewer.update();
	window.wp.hooks.doAction( '3dprint-lite.drawPrinterBox');
}

function p3dliteMakeGroundPlane() {

	var printer_dim=new Array();
	printer_dim.x=jQuery('input[name=product_printer]:checked').data('length')
	printer_dim.y=jQuery('input[name=product_printer]:checked').data('width')
	printer_dim.z=jQuery('input[name=product_printer]:checked').data('height')
	var scene = p3dlite_viewer.getScene();
	var sceneBox=p3dlite.aabb;

	var planeCenter = scene.aabb.center();
	var planeHalfSize = 1.5 * Math.max(sceneBox.maxX, sceneBox.maxY, sceneBox.minX+printer_dim.x, sceneBox.minY+printer_dim.y);
	var planeMinX = planeCenter[0] - planeHalfSize;
	var planeMinY = planeCenter[1] - planeHalfSize;
	var planeZ = sceneBox.minZ;
	var numOfGridsPerDimension = 10;
	var sizePerGrid = 2 * planeHalfSize / numOfGridsPerDimension;
	var groundPlane = new JSC3D.Mesh('groundplane');

	groundPlane.vertexBuffer = [];
	for (var i=0; i<=numOfGridsPerDimension; i++) {
		for (var j=0; j<=numOfGridsPerDimension; j++) {
			groundPlane.vertexBuffer.push(planeMinX + j * sizePerGrid, planeMinY + i * sizePerGrid, planeZ );
		}
	}

	groundPlane.indexBuffer = [];
	for (var i=0; i<numOfGridsPerDimension; i++) {
		for (var j=0; j<numOfGridsPerDimension; j++) {
			groundPlane.indexBuffer.push(
				i * (numOfGridsPerDimension + 1) + j, 
				(i + 1) * (numOfGridsPerDimension + 1) + j, 
				(i + 1) * (numOfGridsPerDimension + 1) + j + 1, 
				i * (numOfGridsPerDimension + 1) + j + 1, 
				-1 
				);
		}
	}

	groundPlane.isDoubleSided = true;	
	groundPlane.init();
	groundPlane.calcAABB();

	groundPlane.setRenderMode('wireframe');
	groundPlane.setMaterial(new JSC3D.Material('plane', 0, p3dlite.plane_color, 0));
	scene.addChild(groundPlane);
	scene.calcAABB();
}

function p3dliteSignedVolume(p1, p2, p3) {

	v321 = p3[0]*p2[1]*p1[2];
	v231 = p2[0]*p3[1]*p1[2];
	v312 = p3[0]*p1[1]*p2[2];
	v132 = p1[0]*p3[1]*p2[2];
	v213 = p2[0]*p1[1]*p3[2];
	v123 = p1[0]*p2[1]*p3[2];
	return (1.0/6.0)*(-v321 + v231 + v312 - v132 - v213 + v123);
}
function p3dliteSurfaceArea(p1, p2, p3) {

	ax = p2[0] - p1[0];
	ay = p2[1] - p1[1];
	az = p2[2] - p1[2];
	bx = p3[0] - p1[0];
	by = p3[1] - p1[1];
	bz = p3[2] - p1[2];
	cx = ay*bz - az*by;
	cy = az*bx - ax*bz;
	cz = ax*by - ay*bx;
	return 0.5 * Math.sqrt(cx*cx + cy*cy + cz*cz);
}    

function p3dliteDialogCheck() {
	if (p3dlite.file_selected>0)
		jQuery('#p3dlite-container input[type=file]').parent().css('z-index', '999')
	p3dlite.file_selected++;
}

if (window.FileReader && window.FileReader.prototype.readAsArrayBuffer) {
	p3dlite.filereader_supported=true;
} else {
	p3dlite.filereader_supported=false;
}