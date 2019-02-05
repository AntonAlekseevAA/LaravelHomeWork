$(document).ready(function(){
//TODO Remove. Get data via api
var tree = [
        {
            "name": "aa",
            "commentid": 1,
            "comment": "Root1",
            "votes": 7,
            "reply": 1,
            "votedByUser": 1,
            "vote": "up",
			'level': 1,
            "nodes": [
                {
                    "name": "nasyanasya86->aa",
                    "commentid": 2,
                    "comment": "Nested1",
                    "votes": 0,
                    "votedByUser": 0,
                    "vote": 0,
                    "date": "2019-02-03 09:08:43",
                    "reply": 2,
					'level': 2,
                    "nodes": [
                        {
                            "name": "nasyanasya86->aa",
                            "commentid": 3,
                            "comment": "NestedNested1",
                            "votes": 0,
                            "votedByUser": 0,
                            "vote": 0,
                            "date": "2019-02-03 09:08:43",
                            "reply": 3,
							'level': 3,
							"nodes": [
								{
									"name": "nasyanasya86->aa",
									"commentid": 4,
									"comment": "NestedNestedNested1",
									"votes": 0,
									"votedByUser": 0,
									"vote": 0,
									"date": "2019-02-03 09:08:43",
									"reply": 3,
									'level': 4,
									"nodes": [
									{
										"name": "nasyanasya86->aa",
										"commentid": 5,
										"comment": "NestedNestedNestedNested1",
										"votes": 0,
										"votedByUser": 0,
										"vote": 0,
										"date": "2019-02-03 09:08:43",
										"reply": 3,
										'level': 5,
									}]
								}]
                        },
                        {
                            "name": "nasyanasya86->aa",
                            "commentid": 4,
                            "comment": "NestedNested2",
                            "votes": 0,
                            "votedByUser": 0,
                            "vote": 0,
                            "date": "2019-02-03 09:08:43",
                            "reply": 4,
							'level': 3,
                        }]
                }],
            "date": "2019-02-02 20:01:41"
        },
        {
            "name": "bb",
            "commentid": 2,
            "comment": "Root1",
            "votes": 7,
            "reply": 1,
            "votedByUser": 1,
            "vote": "up",
			'level': 1,
            "nodes": [
                {
                    "name": "temp243->bb",
                    "commentid": 3,
                    "comment": "Nested1",
                    "votes": 0,
                    "votedByUser": 0,
                    "vote": 0,
                    "date": "2019-02-03 09:08:43",
                    "reply": 2,
					'level': 2,
                    "nodes": [
                        {
                            "name": "temp243->bb",
                            "commentid": 2,
                            "comment": "NestedNested1",
                            "votes": 0,
                            "votedByUser": 0,
                            "vote": 0,
                            "date": "2019-02-03 09:08:43",
                            "reply": 3,
							'level': 3,
                        },
                        {
                            "name": "temp243->bb",
                            "commentid": 3,
                            "comment": "NestedNested2",
                            "votes": 0,
                            "votedByUser": 0,
                            "vote": 0,
                            "date": "2019-02-03 09:08:43",
                            "reply": 4,
							'level': 3,
							"nodes": [
							{
								"name": "temp243->bb",
								"commentid": 2,
								"comment": "NestedNestedNested1",
								"votes": 0,
								"votedByUser": 0,
								"vote": 0,
								"date": "2019-02-03 09:08:43",
								"reply": 3,
								'level': 4,
								"nodes": [
								{
									"name": "temp243->bb",
									"commentid": 2,
									"comment": "NestedNestedNestedNested1",
									"votes": 0,
									"votedByUser": 0,
									"vote": 0,
									"date": "2019-02-03 09:08:43",
									"reply": 3,
									'level': 5,
								}]
							}]
                        }]
                }],
            "date": "2019-02-02 20:01:41"
        }];

    var generatedHtml = "";
	
    tree.map(
        x => generatedHtml = MakeComment(x, "comments")
    )

    function MakeComment(x, parentId, depth = 0) {
        var shift = "p-3"


        if (depth > 2) {
            shift = "";
        }

		// Unique comment block id
		var idHash = md5(x.commentid-x.level-Date.now()-Math.random());
		
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
        

        if (x.hasOwnProperty("nodes")) {
			GetInnerComments(x.nodes, depth + 1, `${idHash}`)
        }
    }
	
    function GetInnerComments(arrayComments, depth, parentId) {
		
        if (arrayComments.length == 0) {
            return;
        }
		
        var resultHtml = '';

        arrayComments.forEach(element => {
           MakeComment(element, parentId, depth);
        });
    }
    
});