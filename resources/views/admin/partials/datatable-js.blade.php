<script>
$.fn.DataTable.ext.pager.numbers_length = 9;
$(document).ready(function() {
	var dtable = $(".datatable").DataTable({
		@if (Request::has('start'))
			"displayStart": {{ Request::get('start') }}, //https://datatables.net/reference/option/displayStart
		@endif
		"scrollX": true,
		"searching": true,
		"columns": [
		@foreach ($dt['columns']['cols'] as $k)
			@if (!in_array($k, $dt['columns']['exclude']))
				{!! "{name:'" . explode(' AS ', $k)[0] . "'}," !!}
			@endif
		@endforeach
		null
		],
		"columnDefs": [{
			"targets": -1,
			"orderable": false,
            "searchable": false
		}],
		"order": [{!! $dt['columns']['order'] !!}],
		"pageLength": 10,
		"conditionalPaging": true,
		"serverSide": true,
		"ajax": "{{ $dt['ajax'] }}",
		"infoCallback": function (settings, start, end, max, total, pre) {
			$(".datatable tbody tr").click(function() {
				var the_url = $(this).find(".edit").attr("href");
				if (the_url != null && the_url.length > 0) {
					window.location.href = the_url;
				}
			});
			
			$(".datatable tbody tr a.delete").click(function(event) {
				event.preventDefault();
				var the_url = $(this).attr("href");
				if(confirm("Are you sure you want to delete the selected entry?")){
					$.ajax({type: "POST", url: the_url, 
						success: function (transport) {
							dtable.draw();
						}, 
						error: function(jqXHR, exception) {
							alert("There was an error deleting the selected entry.");
						},
						data: {"_token": "{{ csrf_token() }}"}
					});
				}
				event.stopPropagation();
			});

            $(".datatable tbody tr a.login").click(function(event) {
                event.preventDefault();
                var the_url = $(this).attr("href");
                if(confirm("Are you sure you want to log in as this user? This will log you out of the admin panel.")){
                    window.location.href = the_url;
                }
                event.stopPropagation();
            });

			if (total > 0) {
				return "SHOWING <strong>" + start + "</strong> TO <strong>" + end + "</strong> OF <strong>" + total + "</strong>";
			} else {
				return "";
			}
		},
		"language": {
			"emptyTable": "<div style=\"text-align:center;\">There are no entries to display.</div>",
			"zeroRecords": "<div style=\"text-align:center;\">No matching entries found for your search.</a>"
		},
	});
	$(".datatable").on("preXhr.dt", function () {
		$("#spinner").show();
	});
	$(".datatable").on("xhr.dt", function () {
		$("#spinner").hide();
	});
	$("#global_search_form").show();
	$("#global_search_box").keyup($.debounce(500, function () {
	    dtable.search( this.value ).draw();
	}));
});
</script>
