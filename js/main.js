/* Author:

*/

winWidth = window.innerWidth;
winHeight = window.innerHeight;
$window = $(window);


$(function() {
    initLayout();
    initInteractive();
    initPlaceholder();
    $window.bind('resize', function() {
        winWidth = window.innerWidth;
        winHeight = window.innerHeight;
        initLayout();
    });
});


$('[contenteditable]').live('focus', function() {
    var $this = $(this);
    $this.data('before', $this.html());
    return $this;
}).live('blur keyup paste', function() {
    var $this = $(this);
    if ($this.data('before') !== $this.html()) {
        $this.data('before', $this.html());
        $this.trigger('change');
    }
    return $this;
});


$('figure.forumextra').live('mousedown', function(e) {
    e.preventDefault();
});

$('figure.forumextra').live('click', function() {
    $this = $(this);

    html = $('<div>').append($this.clone().removeClass('forumextra').addClass('activeforumextra')).html();
    insertForumExtra(html);
});

function initPlaceholder() {
    $("[placeholder]").each(function() {
        console.debug($(this));
        $(this).textPlaceholder();

    });
}

function doAction(action) {
    console.log(action);
}

function initInteractive() {
    initButtons();
    initForms();
}

function initButtons() {
    $('.favbutton').live('click', function() {
        $this = $(this);
        
        $this.toggleClass('btn-success');

        if ($this.hasClass('decoy')) {


            

        } else {
            $.post(
                $this.attr('href'), 
                {},
                function(data) {
                    if (data.action == 'addfav') {
                        $this.addClass('btn-success');
                        $this.attr('title', 'Remove from Favorites');
                        $this.attr('href', 'unfavorite');
                    } else if (data.action == 'unfav') {
                        $this.removeClass('btn-success');
                        $this.attr('title', 'Add as Favorite');
                        $this.attr('href', 'favorite');
                    }

                    doAction(data.action);
                },
                'json'
            );
        }

        


        return false;
    });

    $('.homebg').live('change', function() {
        $this = $(this);
        checked = $this.is(':checked');
        if (checked) result = { action: 1, id: $this.val() };
        else result = { action: 0, id: $this.val() };
        $.post('homebg', result, function(data) {
            doAction(data.action);
        }, 'json');
    });

    $('input.winner').live('change', function() {
        $this = $(this);

        if ($this.is(':checked')) {
            val = 1;
        } else {
            val = 0;
        }

        $.get('', {winner: val}, function(data) {
            console.log(data);
        });
    });
}

function initForms() {
    $('.form-ajax').live('submit', function(e) {
        $this = $(this);
        queryString = $this.serialize();
        $.ajax({
            url: $this.attr('action') + '?' + queryString, 
            dataType: 'json',
            success: function(data) {
                doAction(data.action);
                $this.trigger('dataReceived',data);
            }
        });
        return false;
    });
}




function initLayout() {
    resizeArt();
    resizeSingleArt();
}

function resizeArt() {
    scale = 0.8;
    $('img.art.large').each(function() {

            $this = $(this);
            newWidth = fitWidth(
                winWidth, 
                winHeight, 
                $this.attr('data-width'), 
                $this.attr('data-height')
            );
            $this.width(newWidth + 'px');
            $this.parents('figure').find('.captioninner').width(newWidth + 'px');



    });


}

function resizeSingleArt() {
    scale = 0.9;

    $('img.singleart').each(function() {
        $this = $(this);
        $wrapper = $(this).parent();
        offset = $this.offset();
        $wrapper.width('auto');
        frameHeight = winHeight - offset.top*1.25;
        frameWidth = $wrapper.width();
        width = $this.attr('data-width');
        height = $this.attr('data-height');
        newWidth = fitWidth(
            frameWidth,
            frameHeight,
            width,
            height
        );

        ratio = width/height;
        newHeight = newWidth/ratio;

        $this.width(newWidth + 'px');
        $this.parent().width(newWidth);
        $this.parent().height(newHeight);

    });
}

function fitWidth(frameWidth, frameHeight, width, height) {
            frameRatio = frameWidth/frameHeight;
            innerRatio = width/height;
            if (frameRatio > innerRatio) {
                newHeight = frameHeight * scale;
                newWidth = newHeight * innerRatio;
            } else {
                newWidth = frameWidth * scale;
                newHeight = frameWidth / innerRatio;
            }

            if (newHeight < height) {
                return newWidth;
            } else {
                return width;
            }
}




function updateHiddenInput($el) {
    $el.siblings('input.easeditorinput-' + $el.attr('name')).val(
        $el.html()
    );
}


function insertForumExtra(extra) {
    $editor = $('.easeditor');
    if ($editor.is(':focus')) {
        pasteHtmlAtCaret(extra);
    } else {
        $editor.html($editor.html() + extra);
    }
}



function pasteHtmlAtCaret(html) {
    var sel, range;
    if (window.getSelection) {
        // IE9 and non-IE
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // non-standard and not supported in all browsers (IE9, for one)
            var el = document.createElement("div");
            el.innerHTML = html;
            var frag = document.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
                lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);

            // Preserve the selection
            /*if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }*/
        }
    } else if (document.selection && document.selection.type != "Control") {
        // IE < 9
        document.selection.createRange().pasteHTML(html);
    }
}