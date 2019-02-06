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
		generatedHtml = MakeComment(tree[i], "comments");
	}
	

    function MakeComment(x, parentId, depth = 0) {
		
		// Unique comment block id
		var idHash = md5(x.id-x.level-Date.now()-Math.random());
		
		// TODO extract template into var
		// static img for example. In real world we can store filename in user profile and insert path inline.
		
        document.getElementById(parentId).innerHTML = document.getElementById(parentId).innerHTML + (`<div class="media">
            <img style="width:30px;" />
            <div class="media-body pt-3">
				<div class="border">
				<img src="images/img_avatar3.png" alt="x.name" class="pt-2 pl-2 rounded-circle" style="width:50px;">
					  <h4>${x.name} <small><i>${x.date}</i></small></h4>
					  <p class="h6 small">${x.comment}</p>
					  <button type="button" class="btn btn-dark btn-sm btnSendComment">Comment</button>
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
			MakeComment(arrayComments[i], parentId, depth);
		}
    }
    
});