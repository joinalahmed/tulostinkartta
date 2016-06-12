/**
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */
jQuery(document).ready(function() {
	jQuery( "#p3dlite_tabs" ).tabs();
	jQuery( ".p3dlite_color_picker" ).wpColorPicker();

});

function p3dliteAddPrinter() {
	jQuery('table.printer').first().clone().insertBefore('#add_printer_button').find('input').val('');
	jQuery('table.printer').last().find('.remove_printer').remove();
	jQuery('table.printer').last().find('.item_id').remove();
	jQuery('table.printer').last().find('tr.printer_materials').remove();

}

function p3dliteAddMaterial() {
	jQuery('table.material').first().clone().insertBefore('#add_material_button').find('input').val('');
	jQuery('table.material').last().find('.wp-picker-container').remove();
	jQuery('table.material').last().find('td.color_td').html('<input type="text" class="p3dlite_color_picker" name="p3dlite_material_color[]" value="" />');
	jQuery('table.material').last().find( ".p3dlite_color_picker" ).wpColorPicker();
	jQuery('table.material').last().find('.remove_material').remove();
	jQuery('table.material').last().find('.item_id').remove();
}

function p3dliteAddCoating() {
	jQuery('table.coating').first().clone().insertBefore('#add_coating_button').find('input').val('');
	jQuery('table.coating').last().find('.wp-picker-container').remove();
	jQuery('table.coating').last().find('td.color_td').html('<input type="text" class="p3dlite_color_picker" name="p3dlite_coating_color[]" value="" />');
	jQuery('table.coating').last().find( ".p3dlite_color_picker" ).wpColorPicker();
	jQuery('table.coating').last().find('.remove_coating').remove();
	jQuery('table.coating').last().find('.item_id').remove();
	jQuery('table.coating').last().find('tr.coating_materials').remove();
}

function p3dliteRemovePrinter(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-1" method="post"><input type="hidden" name="action" value="remove_printer"><input type="hidden" name="printer_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveMaterial(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-2" method="post"><input type="hidden" name="action" value="remove_material"><input type="hidden" name="material_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveCoating(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-3" method="post"><input type="hidden" name="action" value="remove_coating"><input type="hidden" name="coating_id" value="'+id+'"></form>' ).appendTo('body').submit()
}
function p3dliteRemoveRequest(id) {
	jQuery( '<form action="admin.php?page=3dprint-lite#p3dlite_tabs-4" method="post"><input type="hidden" name="action" value="remove_request"><input type="hidden" name="request_id" value="'+id+'"></form>' ).appendTo('body').submit()
}

function p3dliteSetMaterialType(obj)  {
        var material_type = obj.value;
	jQuery(obj).closest('table.form-table.material').find('tr, a').each(function(i, el){
		var className = jQuery(el).attr('class');
		if (typeof(className)!=='undefined') {
			if (className.indexOf('material')==0) {

				if (className=='material_'+material_type) jQuery(el).show();
				else jQuery(el).hide();
			}
		}
	});
}

jQuery(document).ready(function(){
	jQuery('.sumoselect').SumoSelect({ okCancelInMulti: true, selectAll: true });
});