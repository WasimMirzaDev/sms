$(document).ready(function() {

  pageSetUp();


      "use strict";

      var date = new Date();
      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();

      var hdr = {
          left: 'title',
          center: 'month,agendaWeek,agendaDay',
          right: 'prev,today,next'
      };

      var initDrag = function (e) {
          // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
          // it doesn't need to have a start or end

          var eventObject = {
              title: $.trim(e.children().text()), // use the element's text as the event title
              description: $.trim(e.children('span').attr('data-description')),
              icon: $.trim(e.children('span').attr('data-icon')),
              className: $.trim(e.children('span').attr('class')) // use the element's children as the event class
          };
          // store the Event Object in the DOM element so we can get to it later
          e.data('eventObject', eventObject);

          // make the event draggable using jQuery UI
          e.draggable({
              zIndex: 999,
              revert: true, // will cause the event to go back to its
              revertDuration: 0 //  original position after the drag
          });
      };

      var addEvent = function (title, priority, description, icon) {
          title = title.length === 0 ? "Untitled Event" : title;
          description = description.length === 0 ? "No Description" : description;
          icon = icon.length === 0 ? " " : icon;
          priority = priority.length === 0 ? "label label-default" : priority;

          var html = $('<li><span class="' + priority + '" data-description="' + description + '" data-icon="' +
              icon + '">' + title + '</span></li>').prependTo('ul#external-events').hide().fadeIn();

          $("#event-container").effect("highlight", 800);

          initDrag(html);
      };

      /* initialize the external events
     -----------------------------------------------------------------*/

      $('#external-events > li').each(function () {
          initDrag($(this));
      });

      $('#add-event').click(function () {
          var title = $('#title').val(),
              priority = $('input:radio[name=priority]:checked').val(),
              description = $('#description').val(),
              icon = $('input:radio[name=iconselect]:checked').val();

          addEvent(title, priority, description, icon);
      });

      /* initialize the calendar
     -----------------------------------------------------------------*/

    var calendar =   $('#calendar').fullCalendar({

          header: hdr,
          editable: false,
          droppable: false,
          selectable: true,
          selectHelper: true,
          events: "/events/show",
          // events: [{
          //   id: 10,
          //   title: "29 to 30 event",
          //   start: "2022-04-27T00:00:00",
          //   end: "2022-04-28T00:00:00",
          //   color: "navy",
          //   fullDay: "true",
          // }],
          select: function (start, end, allDay) {
           start=moment(start).format('Y-MM-DD HH:mm');
           end = new Date(end);
           end.setDate(end.getDate()-1);
           end=moment(end).format('Y-MM-DD HH:mm');

           addNewEvent(start, end, 0);
             var title = false;
             if (title) {

                 $.ajax({
                     url: "/events/create",
                     data: {
                         title: title,
                         start: start,
                         end: end,
                         type: 'add'
                     },
                     type: "POST",
                     success: function (data) {
                         alert("Event Created Successfully");
                         calendar.fullCalendar('renderEvent',
                         {
                           id: data.id,
                           title: title,
                           start: data.start,
                           end: data.end,
                           allDay: allDay
                      },true);
                         calendar.fullCalendar('unselect');
                     }
                 });
             }
         },



         eventDrop: function (event, delta) {
           start=moment(event.start).format('Y-MM-DD HH:mm:ss');
           end=moment(event.end).format('Y-MM-DD HH:mm:ss');
           $.ajax({
               url: '/events/update',
               data: {
                   title: event.title,
                   start: start,
                   end: end,
                   id: event.id,
                   type: 'update'
               },
               type: "POST",
               success: function (response) {
                   alert("Event Updated Successfully");
               }
           });
       },


                   eventClick: function (event) {
                     addNewEvent('', '', event.id);
                   },


          eventRender: function (event, element, icon) {
              if (event.allDay === 'true') {
                         event.allDay = true;
                 } else {
                         event.allDay = false;
                 }
          },

          windowResize: function (event, ui) {
              $('#calendar').fullCalendar('render');
          }
      });

      /* hide default buttons */
      $('.fc-right, .fc-center').hide();


    $('#calendar-buttons #btn-prev').click(function () {
        $('.fc-prev-button').click();
        return false;
    });

    $('#calendar-buttons #btn-next').click(function () {
        $('.fc-next-button').click();
        return false;
    });

    $('#calendar-buttons #btn-today').click(function () {
        $('.fc-today-button').click();
        return false;
    });

    $('#mt').click(function () {
        $('#calendar').fullCalendar('changeView', 'month');
    });

    $('#ag').click(function () {
        $('#calendar').fullCalendar('changeView', 'agendaWeek');
    });

    $('#td').click(function () {
        $('#calendar').fullCalendar('changeView', 'agendaDay');
    });

});
function myDataTable()
{

  $('#mydatatable').DataTable().destroy();
  var otable =  $('#mydatatable').DataTable(
  		{
  			"bPaginate": false,
        "ordering" : false
  		}
  );
  $("#mydatatable thead th input[type=text]").on( 'keyup change', function () {

      otable
          .column( $(this).parent().index()+':visible' )
          .search( this.value )
          .draw();

  });

}
function check_all(state)
{
  var state = $(state).is(':checked');
    $(".student_id").prop("checked", state);
}


function saveEvent()
{
	$("#event_form").on('submit',(function(e){
    let formAction = $(this).attr('action');
  	$(".overlay").show();
  	$("button.save").prop("disabled",true);
		var edit_id = $("input[name=id]").val();
     e.preventDefault();
  		$.ajax({
  			url: formAction, // Url to which the request is send
  			type: "POST",             // Type of request to be send, called as method
  			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
  			contentType: false,       // The content type used when sending data to the server.
  			cache: false,             // To unable request pages to be cached
  			processData:false,        // To send DOMDocument or non processed data file it is set to false
  			success: function(response)   // A function to be called if request succeeds
  			{
  				$( "#event_form" ).unbind( "submit");
  				if(response['success']==1)
 					{
  					_success(response['msg']);
						$("#event_modal").modal('hide');
  					$("button.save").prop("disabled",false);
  					var data = response['data'];
  					var id = data['id'];
            $("#calendar").fullCalendar('removeEvents', data['id']);
            $("#calendar").fullCalendar('renderEvent',
            {
              id:    data['id'],
              title: data['title'],
              start: data['start'],
              end:   data['end'],
              color: data['color'],
              allDay: false
         },true);
            $("#calendar").fullCalendar('unselect');
  					$(".overlay").hide();
  				}
  				else
  				{
  					var error = "";
  					for(var a = 0; a<response['msg'].length; a++)
  					{
  						error += response['msg'][a] + "<br/>";
  					}
  					_error(error);
  					$("button.save").prop("disabled",false);
  					$(".overlay").hide();
  				}
  			}
  		});
   }));
}
function addNewEvent(start='', end='', id)
{
  $.ajax({
    url: '/events/add',
    method: 'post',
    data: {start:start, end:end, id:id},
    success: function(response)
    {
     $("#event_modal_detail").html(response.html);
     $("#event_modal").modal('show');
     myDataTable();
    }
  });
}


function deleteEvent(id)
{
	$.SmartMessageBox({
		title : "Warning!",
		content : "Are you sure ? Do you want to delete this?",
		buttons : '[No][Yes]'
	}, function(ButtonPressed) {
		if (ButtonPressed === "Yes") {
			$(".overlay").show();
			var formAction = "/events/delete";
		 $.post(formAction,{id:id},function(data){
			 if(data['success'] == 1)
			 {
				 $("#calendar").fullCalendar('removeEvents', data['id']);
         $("#event_modal").modal('hide');
				 _success(data['msg']);
			 }
			 else
			 {
				 _error(data['msg']);
			 }
			 $(".overlay").hide();
		 });
		}
		if (ButtonPressed === "No")
		{
		 _error('You pressed No');
		}
	});
}
