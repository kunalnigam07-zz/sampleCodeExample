@extends('admin.layouts.base')
@section('body')
<p class="dox-detail-list">
These give access to the Frequently Asked Questions which are split into two sections, one set of information for not-logged-in viewers and logged-in Members, with a second set of information which can be accessed once an Instructor logs in. Most of these are generic and probably won’t need to be changed but there is the option to do so, should the Administrator so wish.<br><br>

The Administrator also has the option to add new entries by clicking on the ‘Create New’ icon in the top right-hand corner of the page.<br><br>

•   <strong>Category</strong> – This relates to the Header as it appears on the list of FAQs on the Member or Instructor viewing screen.<br>
•   <strong>Section</strong> – This depicts whether the information appears in the Member or Instructor FAQs<br>
•   <strong>Ordering</strong> – This depicts where the Category or Header appears in the list of FAQs<br><br>

By clicking on the ‘pen’ icon in any given row, the Administrator will be taken through to another page, displaying details relating to the individual entry<br><br>

•   <strong>Question</strong> – this is the description that appears in the dropdown menu from the category to which it has been allocated (detailed towards the bottom of the page)<br>
•   <strong>Answer</strong> – This is where the Administrator has the option to amend the main bulk of the information stored under the specified Category<br>
•   <strong>Ordering</strong> – this relates to what position in the list of sub headers under ‘Category’, that the ‘Question’ appears. This can be amended by adjusting the number quoted.
</p>
@include('admin.partials.datatable')
@stop
@section('extra_footer')
@include('admin.partials.datatable-js')
@stop
