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
			alert( "success" );
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
        var shift = "p-3"


        if (depth > 2) {
            shift = "";
        }

		// Unique comment block id
		var idHash = md5(x.id-x.level-Date.now()-Math.random());
		
		// TODO extract template into var
        document.getElementById(parentId).innerHTML = document.getElementById(parentId).innerHTML + (`<div class="media ${shift}">
            <img src="images/img_avatar3.png" alt="x.name" class="mr-3 mt-3 rounded-circle" style="width:70px;">
            <div class="media-body">
              <h4>${x.name} <small><i>Posted on ${x.date}</i></small></h4>
              <p>${x.comment}</p>
			  
			  <button type="button" class="btn btn-dark">Comment</button>
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