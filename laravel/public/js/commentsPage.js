var commentsContainer = `<div id="comments" class="card-body border border-right border-2 comment-block" data-id="0">

						</div>
						<div class="form-group col-md-12 pt-2" style="padding:0;" data-id="0">
							<textarea class="form-control comments-textarea mb-2" id="exampleFormControlTextarea1" rows="3" data-id="0"></textarea>
							<button type="button" class="col-md-6 float-right btn btn-secondary btn-sm btnSendComment" onClick="createNewComment()" data-id="0">Comment</button>
						</div>`;

function loadComments(sortField, sortOrder) {
    var tree;
    $.ajax({
        type: 'GET',
        url: `/api/comments?sortField=${sortField}&sortOrder=${sortOrder}`,
        dataType: "json",
        async: false,	// need await. All actions available only when page full loaded.
    })
        .done(function (data) {
            console.log(data);
            tree = data;
			
			$('.comment-wrapper').html('');
			$('.comment-wrapper').append(commentsContainer);
        })
        .fail(function () {
            alert("error");
        });

    var generatedHtml = "";

    for (var i in tree) {
        generatedHtml = makeComment(tree[i], "comments");
    }
}

$(document).ready(function(){
//TODO Remove. Get data via api

    loadComments("date", "desc");

    var userId = $('#hfUserId').attr('value');
	
	if (!userId) {
		$('.btnSendComment').remove();
		$('textarea').remove();
	}
	
	fetchNotSeenComments(userId);
	
	var comments = $('.comment-item');
	
	$.each(comments, function(i, item) {
		item.addEventListener("mouseover", function() {
			var userId = $('#hfUserId').attr('value');
	
				if (!$(event.target).hasClass('comment-block-new')) {
					return;
				}
			
				if (!userId) {
					return;
				}
	
				var comment = $(event.target); // store event sender. In ajax success handler event.sender is XMLHttpRequest because he trigger callback function.
				var commentId = $(event.target).attr('data-id');
			
				$.ajax({
					type: 'POST',
					url: `api/comments/setSeen`,
					dataType: "json",
					async:true,
					data:
					{
						"userId": userId,
						"commentId": commentId
					}
				})
					.done(function(data) {
						console.log(data);
						
						comment.removeClass('comment-block-new');
						
					})
					.fail(function() {
						alert( "error" );
					});
			}, {once : true});
	});
});

// Use Deep first search to draw  comments tree
function makeComment(x, parentId, depth = 0) {
	
		var idHash = md5(x.id-x.level-Date.now()-Math.random());
		
		// static img for example. In real world we can store filename in user profile and insert path inline.
		
        document.getElementById(parentId).innerHTML = document.getElementById(parentId).innerHTML + (`<div class="media ${x.id}" data-id="${x.id}" data-parent-id="${x.reply_id}">
            <img style="width:30px;" />
            <div class="media-body pt-3 pl-2" data-id="${x.id}">
				<div class="border rounded-top rounded-top-2 comment-item" onClick="displayEditPanel()" data-id="${x.id}">
				<img src="images/img_avatar3.png" alt="x.name" class="pt-2 pr-2 rounded-circle float-xl-right" style="width:50px;">
				<h5 class="float-xl-right">${x.name}</h5>
					  <small class="float-sm-left pl-2 border rounded-left"><i>${x.date}</i></small>
					  <div class="col-md-11 mt-5 pl-2 border rounded-bottom">
						<p class="h6" style="word-break: break-word; min-height: 5vh!important; text-align:left;">${x.comment}</p>
					  </div>
					  
					  <p class="votesCount ml-1" data-id="${x.id}">${x.votes} <i class="fas fa-plus-square ml-1" onclick="plusBtnClick()"></i><i class="fas fa-minus-square" onclick="minusBtnClick()"></i>
					  
						<div>
						<div class="form-group col-md-11 comments-edit-hidden" style="padding:0;" data-id="${x.id}">
							<textarea class="form-control comments-textarea" id="exampleFormControlTextarea1" rows="3" data-id="${x.id}" data-parent-id="${x.reply_id}" data-level="${x.level}"></textarea>
						</div>
					  </div
						</p>
					  <button type="button" class="col-md-6 btn btn-secondary btn-sm mt-1 mb-1 btnSendComment comments-edit-hidden" onClick="createNewComment()" data-id="${x.id}" data-parent-id="${x.reply_id}">Comment</button>
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
	
	var commentText = $(`textarea[data-id="${parentId}"]`).val();
	var userId = $('#hfUserId').attr('value');

	var level = $(`textarea[data-id="${parentId}"]`).attr('data-level');

	if (level >= 5) {
		parentId = $(event.target).attr('data-parent-id');
		level = 4;
	}
	
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
			"comment": commentText
		}
	})
		.done(function(data) {
			console.log(data);
			// tree = data;

			var userName = $('#hfUserName').attr('value');
			
			if (!userName) {
				return;
			}

			appendNested(data.commentId, parentId, userName, commentText, level);
		})
		.fail(function(error) {
			console.log(error);
			alert( "error" );
		});
}

function appendNested(commentid, parentId, userName, commentText, level) {
	
	var hash = md5((commentid)-Date.now()-Math.random());
	
	var blockTemplate = $(`<div class="media ${commentid}" data-id="${commentid}" data-parent-id="${parentId}">
            <img style="width:30px;" />
            <div class="media-body pt-3 pl-2" data-id="${commentid}">
				<div class="border rounded-top rounded-top-2 comment-item" onClick="displayEditPanel()" data-id="${commentid}">
				<img src="images/img_avatar3.png" alt="${userName}" class="pt-2 pr-2 rounded-circle float-xl-right" style="width:50px;">
				<h5 class="float-xl-right">${userName}</h5>
					  <small class="float-sm-left pl-2 border rounded-left"><i>${Date.now()}</i></small>
					  <div class="col-md-11 mt-5 pl-2 border rounded-bottom">
						<p class="h6" style="word-break: break-word; min-height: 5vh!important; text-align:left;">${commentText}</p>
					  </div>
					  
					  <p class="votesCount ml-1" data-id="${commentid}">${0} <i class="fas fa-plus-square ml-1" onclick="plusBtnClick()"></i><i class="fas fa-minus-square" onclick="minusBtnClick()"></i>
					  
						<div>
						<div class="form-group col-md-11 comments-edit-hidden" style="padding:0;" data-id="${commentid}">
							<textarea class="form-control comments-textarea" id="exampleFormControlTextarea1" rows="3" data-id="${commentid}" data-parent-id="${parentId}" data-level="${level}"></textarea>
						</div>
					  </div
						</p>
					  <button type="button" class="col-md-6 btn btn-secondary btn-sm mt-1 mb-1 btnSendComment comments-edit-hidden" onClick="createNewComment()" data-id="${commentid}" data-parent-id="${parentId}">Comment</button>
				</div>
              <div id=${hash} class="comments"></div>
            </div>
          </div>`);
	
	var elementToAppend = $(`.media-body[data-id=${parentId}]`);

	if (elementToAppend.length == 0) {
		var elementToAppend = $(`#comments`);
	}
	var rowElement = elementToAppend.html();
	
	elementToAppend.append(blockTemplate);
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

function sortByDate() {
    var sender = event.target;
    var sortOrder = $(sender).attr('data-direction');

    loadComments("date", sortOrder);

    var newSortOrder = sortOrder == 'desc' ? 'asc' : 'desc';
    $(event.target).attr('data-direction', newSortOrder);
}

function sortByVotes() {
    var sender = event.target;
    var sortOrder = $(sender).attr('data-direction');

    loadComments("votes", sortOrder);

    var newSortOrder = sortOrder == 'desc' ? 'asc' : 'desc';
    $(event.target).attr('data-direction', newSortOrder);
}

function displayEditPanel() {
	var commentId = $(event.target).attr('data-id');
	$(`.form-group[data-id="${commentId}"]`).removeClass('comments-edit-hidden');
	$(`button[data-id="${commentId}"]`).removeClass('comments-edit-hidden');
}

function fetchNotSeenComments(userId) {
	if (!userId) {
		return;
	}

	$.ajax({
        type: 'POST',
        url: `api/comments/getNotSeenComments`,
        dataType: "json",
        async:true,
		data:
		{
			"userId": userId
		}
	})
		.done(function(data) {
			console.log(data);

			$.each(data, function(i, item) {
				// comment-block-new
				var id = item.comment_id;
				$(`.comment-item[data-id="${id}"]`).addClass('comment-block-new');
			});

		})
		.fail(function() {
			alert( "error" );
		});
}
