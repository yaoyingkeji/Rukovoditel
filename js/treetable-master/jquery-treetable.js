jQuery.fn.extend({
	treetable: function() {
		var $table = $(this);
		$table.addClass("tt-table");

		var $items = $table.find("div.tt");
		var index = {};
		var items = [];    
                var apply_rlt = $(this).hasClass()

		// add items to index
		$items.each(function (i, el) {
			var $el = $(el);
			var id = $el.data('tt-id');
			var parent = $el.data('tt-parent');

			var item = {
				id: id,
				parent: parent,
				children: [],
				el: $el,
				left: 0,
				width: $el.width() + 22
			};

			index[id] = item;
			items.push(item);
		});
                
                //console.log(items);

		// make a graph from parent relations
		items.forEach(function (item) {
			if (item.parent !== undefined) {
				item.parent = index[item.parent];
				item.parent.children.push(item);
			}
		});

		// pad items
		items.forEach(function (item) {

			item.left = 0;
			if (item.parent !== undefined) {
				item.left = item.parent.left + item.parent.width;
			}
                        
                        item.el.after('<div class="tt-content-padding" style="width:'+item.left+'px"></div>')
		});

		// position items
		items.forEach(function (item) {
			//console.log(el.left);
                        
                        //prepare value for rlt direction
                        if(app_language_text_direction=='rtl' && item.left>0)
                        {
                           item.left = 0-item.left;
                        }
                        
			item.el.css("left", item.left);
		});

		// wrap contents
		items.forEach(function (item) {
			item.el.html('<div class="content">' + item.el.html() + '</div>');
		});

		// add parent classes
		items.forEach(function (item) {
			if (item.children.length > 0) {
				item.el.addClass("tt-parent");
				item.showChildren = true;
			}
		});

		// draw lines
		items.forEach(function (item) {

			if (item.parent === undefined) {
				return;
			}

			var childPos = item.el.position();
			var parent = item.parent;

			var parentPos = parent.el.position();
			var height = childPos.top - parentPos.top;
			var width = item.left - parent.left;
			var left = parent.left - item.left + (parent.width / 2);
                        
                        //prepare value for rlt direction
                        if(app_language_text_direction=='rtl')
                        {
                            width = 24;
                            left = 11;                           
                        }
                                                
			var $tail = $('<div class="tail"></div>').css({
				height: height,
				width: width,
				left: left
			});

			item.el.prepend($tail);                        
		});
                
                $table.on("click", "div.tt-parent", function (e) {
                    let sort_url = $(this).attr('data-tt-sort-url')
                    if(sort_url)
                    {
                        open_dialog(sort_url)
                    }
                })

		// handle click event
                /*
		$table.on("click", "div.tt div.content", function (e) {

			var $el = $(e.currentTarget).closest(".tt");
			var $tr = $el.closest("tr");
			var id = $el.data('tt-id');
			var item = index[id];

			if (item.showChildren === true) {
				// hide all children
				item.showChildren = false;

				function hide(parentId) {
					var item = index[parentId];
					item.children.forEach(function (child) {
						if (child.showChildren !== undefined) {
							child.showChildren = false;
						}

						$(child.el).closest("tr").addClass("tt-hide");
						hide(child.id);
					});
				}

				hide(id);
			}
			else {
				// show direct children
				item.showChildren = true;
				item.children.forEach(function (child) {
					$(child.el).closest("tr").removeClass("tt-hide");
				});
			}
		});
                 */
                 

		// initially hide all children
                /*
		items.forEach(function (item) {

			if (item.parent === undefined && item.children.length > 0) {
				item.el.find(".content").click();
			}
		});
             * */
                 
	}
});

