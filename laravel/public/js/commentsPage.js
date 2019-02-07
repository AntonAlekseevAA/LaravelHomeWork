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
		// commentId + 1 Нельзя делать, так как уже могут быть блоки с таким id, которые идут после.Его может вообще не надо указывать
		
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
					  <small class="float-sm-left pl-2 border rounded-left"><i>${x.date}</i></small>
					  <div class="col-md-11 mt-5 pl-2 border rounded-bottom">
						<p class="h6" style="word-break: break-word; min-height: 15vh!important;">${x.comment}</p>
					  </div>
					  
					  <p class="votesCount ml-1" data-id="${x.id}">${x.votes} <i class="fas fa-plus-square ml-1" onclick="plusBtnClick()"></i><i class="fas fa-minus-square" onclick="minusBtnClick()"></i>
					  
						<!--<p class="h6" style="word-break: break-word; min-height: 15vh!important;">${x.comment}</p>-->
						<!--<div class="col-md-11 border rounded-bottom">-->
						<div>
						<div class="form-group col-md-11" style="padding:0;">
							<label for="exampleFormControlTextarea1">Example textarea</label>
							<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
						</div>
					  </div
						</p>
					  <button type="button" class="col-md-11 btn btn-dark btn-sm mt-1 mb-1 btnSendComment" onClick="createNewComment()" data-id="${x.id}">Comment</button>
				</div>
              <div id=${idHash} class="comments"></div>
            </div>
          </div>`);
        

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
        async:true,
		data:
		{
			"users_id": userId,
			"reply_id": parentId,
			"comment": comment
		}
	})
		.done(function(data) {
			console.log(data);
			// tree = data;
			appendNested(data.commentId);
		})
		.fail(function() {
			alert( "error" );
		});
}

// commentId + 1 Нельзя делать, так как уже могут быть блоки с таким id, которые идут после.Его может вообще не надо указывать
function appendNested(commentid) {
	
	var hash = md5((commentid)-Date.now()-Math.random());
	var blockTemplate = $(`<div class="media ${commentid}" data-id="${commentid}" data-parent-id="${commentid}">
            <img src="img_avatar3.png" alt="hfUserNameValue" class="mr-3 mt-3 rounded-circle" style="width:45px;">
            <div class="media-body" data-id="${commentid}">
              <h4>hfUserNameValue<small><i>Posted on Date.now()</i></small></h4>
			  <i class="fas fa-plus-square" onclick="plusBtnClick()"></i>
			  <i class="fas fa-minus-square" onclick="minusBtnClick()"></i>
			  <p class="votesCount" data-id="${commentid}">0</p>
              <p>textFieldValue</p>
			  <button data-id="${commentid}" onClick="send()">213</button>
              <div id=${hash} class="comments"></div>
            </div>
          </div>`);
	
	
	
	/*var element = $(`.media[data-id=${commentid}]`);
	var newElement = element.clone();
	newElement.attr('data-parent-id', commentid);*/
	
	var elementToAppend = $(`.media-body[data-id=${commentid}]`);
	var rowElement = elementToAppend.html();
	
	//newElement.addClass('testtest');
	// newElement.append($(rowElement));
	(blockTemplate).addClass('testtest');
	elementToAppend.append(blockTemplate);
	//blockTemplate.append(elementToAppend);
}

function plusBtnClick() {
	var elementId = event.target.parentElement.getAttribute('data-id');
	var oldValue = parseInt($(`.votesCount[data-id=${elementId}]`).text());
	
	var userId = $('#hfUserId').attr('value');
	
	if (!userId) {
		return;
	}
	
	$.ajax({
        type: 'POST',
        url: `api/comments/${elementId}/vote`,
        dataType: "json",
        async:true,
		data:
		{
			"users_id": userId,
			"vote":"up"
		}
	})
		.done(function(data) {
			console.log(data);
			$(`.votesCount[data-id=${elementId}]`).text(oldValue + 1);
		})
		.fail(function() {
			alert( "error" );
		});
}

function minusBtnClick() {
	// alert ('MinusBtn Mock');
	
	var userId = $('#hfUserId').attr('value');
	
	if (!userId) {
		return;
	}
	
	var elementId = event.target.parentElement.getAttribute('data-id');
	var oldValue = parseInt($(`.votesCount[data-id=${elementId}]`).text());
	
	$.ajax({
        type: 'POST',
        url: `api/comments/${elementId}/vote`,
        dataType: "json",
        async:true,
		data:
		{
			"users_id": userId,
			"vote":"down"
		}
	})
		.done(function(data) {
			console.log(data);
			$(`.votesCount[data-id=${elementId}]`).text(oldValue - 1);
		})
		.fail(function() {
			alert( "error" );
		});
}