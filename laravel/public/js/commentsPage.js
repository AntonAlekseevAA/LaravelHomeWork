$(document).ready(function(){
//TODO Remove. Get data via api

var tree;
	$.ajax({
        type: 'GET',
        url: '/api/comments/',
        dataType: "json",
        async:false,
	})
		.done(function(data) {
			console.log(data);
			tree = data;
		})
		.fail(function() {
			alert( "error" );
		});
		
    var generatedHtml = "";
	
	for (var i in tree)
	{
		generatedHtml = makeComment(tree[i], "comments");
	}
	
	var userId = $('#hfUserId').attr('value');
	
	if (!userId) {
		$('.btnSendComment').remove();
	}
});

function makeComment(x, parentId, depth = 0) {
		
		// Unique comment block id
		var idHash = md5(x.id-x.level-Date.now()-Math.random());
		
		// TODO extract template into var
		// static img for example. In real world we can store filename in user profile and insert path inline.
		// <h4>${x.name} <small><i>${x.date}</i></small></h4>
		
        document.getElementById(parentId).innerHTML = document.getElementById(parentId).innerHTML + (`<div class="media">
            <img style="width:30px;" />
            <div class="media-body pt-3 pl-2" data-id="${x.id}">
				<div class="border rounded-top rounded-top-2">
				<img src="images/img_avatar3.png" alt="x.name" class="pt-2 pr-2 rounded-circle float-xl-right" style="width:50px;">
				<h5 class="float-xl-right">${x.name}</h5>
					  <small class="float-sm-left pl-2"><i>${x.date}</i></small>
					  <p class="h6 small">${x.comment}</p>
					  <textarea class="col-md-12 mt-5" data-id="${x.id}"></textarea>
					  <button type="button" class="col-md-12 btn btn-dark btn-sm mt-1 mb-1 btnSendComment" onClick="createNewComment()" data-id="${x.id}">Comment</button>
				</div>
              <div id=${idHash} class="comments"></div>
            </div>
          </div>`) 
        

        if (x.hasOwnProperty("children")) {
			GetInnerComments(x.children, depth + 1, `${idHash}`)
        }
    }
	
    function GetInnerComments(arrayComments, depth, parentId) {
		
        if (arrayComments.length == 0) {
            return;
        }
		
        var resultHtml = '';

		console.log(arrayComments);
		
		for (var i in arrayComments)
		{
			makeComment(arrayComments[i], parentId, depth);
		}
    }
	
/* Api requests */

function createNewComment() {
	var parentId = $(event.target).attr('data-id');
	
	var comment = $(`textarea[data-id="${parentId}"]`).val();
	var userId = $('#hfUserId').attr('value');
	
	if (!userId) {
		return;
	}
	
	$.ajax({
        type: 'POST',
        url: '/api/comments/',
        dataType: "json",
        async:false,
		data:
		{
			"users_id": userId,
			"reply_id": parentId,
			"comment": comment
		}
	})
		.done(function(data) {
			console.log(data);
			tree = data;
			// TODO Append to parent
		})
		.fail(function() {
			alert( "error" );
		});
}