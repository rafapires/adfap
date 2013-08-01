var cspSubersPerPage = 10;
jQuery(document).ready(function(){
	jQuery('#cspSubAdminOptsForm [name="sub_enable_checkbox"]').change(function(){
		changeSubEnableOptionCsp();
	});
	changeSubEnableOptionCsp();
	
	jQuery('#cspSubAdminOptsForm').submit(function(){
		jQuery(this).sendFormCsp({
			msgElID: 'cspAdminSubOptionsMsg'
		});
		return false;
	});
	getSubersListCsp();
});
function changeSubEnableOptionCsp() {
	var enabled = jQuery('#cspSubAdminOptsForm [name="sub_enable_checkbox"]').attr('checked');
	jQuery('#cspSubAdminOptsForm [name="opt_values[sub_enable]"]').val(enabled ? '1' : '0');
}
function getSubersListCsp(page) {
	this.page;	// Let's save page ID here, in static variable
	if(typeof(this.page) == 'undefined')
		this.page = 0;
	if(typeof(page) != 'undefined')
		this.page = page;
	
	page = this.page;
	
	jQuery.sendFormCsp({
		msgElID: 'cspAdminSubersMsg'
	,	data: {page: 'subscribe', action: 'getList', reqType: 'ajax', limitFrom: page * cspSubersPerPage, limitTo: cspSubersPerPage}
	,	onSuccess: function(res) {
			if(!res.error) {
				if(page > 0 && res.data.count > 0 && res.data.list.length == 0) {	// No results on this page - 
					// Let's load next page
					getSubersListCsp(page - 1);
				} else {
					toeListable({
						table: '#cspAdminSubersTable'
					,	paging: '#cspAdminSubersPaging'
					,	list: res.data.list
					,	count: res.data.count
					,	perPage: cspSubersPerPage
					,	page: page
					,	pagingCallback: getSubersListCsp
					});
				}
			}
		}
	});
}
function cspSubscrbChangeStatus(link) {
	var id = parseInt(jQuery(link).parents('tr').find('.id').val());
	if(id) {
		jQuery.sendFormCsp({
			msgElID: 'cspAdminSubersMsg'
		,	data: {page: 'subscribe', action: 'changeStatus', reqType: 'ajax', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					if(jQuery(link).hasClass('active')) {
						jQuery(link).removeClass('active').addClass('disabled');
					} else {
						jQuery(link).removeClass('disabled').addClass('active');
					}
				}
			}
		});
	}
}
function cspSubscrbRemove(link) {
	if(confirm(toeLangCsp('Are you sure?'))) {
		var id = parseInt(jQuery(link).parents('tr').find('.id').val());
		if(id) {
			jQuery.sendFormCsp({
				msgElID: 'cspAdminSubersMsg'
			,	data: {page: 'subscribe', action: 'remove', reqType: 'ajax', id: id}
			,	onSuccess: function(res) {
					if(!res.error) {
						getSubersListCsp();
					}
				}
			});
		}
	}
}