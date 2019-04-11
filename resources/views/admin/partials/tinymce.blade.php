<script src="{{ AssetHelper::asset('static/admin/js/tinymce/tinymce.min.js') }}"></script>
<script>
$(document).ready(function() {
@if ($tinymcetype == 'normal')
	tinymce.init({
		selector: ".tinymce",
		plugins: "code link image moxiemanager textcolor charmap table",
        toolbar: "undo redo | styleselect fontsizeselect forecolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image table charmap",
		relative_urls: false,
        extended_valid_elements : "+i[class],+script[id|src|data-load|data-iframe],span",
		content_css : "/static/admin/css/cms-style-normal.css?" + new Date().getTime(),
		height: 250
	 });
@elseif ($tinymcetype == 'email')
    tinymce.init({
        selector: ".tinymce",
        plugins: "code link image moxiemanager textcolor charmap table",
        toolbar: "undo redo | styleselect fontsizeselect forecolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image table charmap",
        relative_urls: false,
        remove_script_host : false,
        height: 250
     });
@endif
});
</script>