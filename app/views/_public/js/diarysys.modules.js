/**
 * diarysys5専用モジュール定義
 */
(function(window) {
if (window.D) return;
window.D = {};

/**
 * 共通設定モジュール
 */
Mixjs.module("Common", D, {
    /** ドキュメントルート */
    _root: "/diarysys5",
    
    /** ホスト */
    _host: location.protocol + "//" + location.host,
    
    /** CookieSuffix */
    _cookieSuffix: ".diarysys.summer-lights.jp",
    
    /** JSONキャッシュ時間 */
    _jsonCacheExpire: {hour: 1},
    
    /** イニシャライザ(オーバーライドして使用) */
    init: function() {
        return this.base;
    },
    
    /** CSRFトークンを取得 */
    getCsrfToken: function() {
        return $("input[name=__CSRF_TOKEN__]").val();
    }
});

/**
 * Utility, Plugin関連モジュール
 */
Mixjs.module("Util", D, {
    /**
     * fancyboxプラグインを起動
     */
    fancybox: function() {
        $('a.fancybox').fancybox();
    },
    
    /**
     * コードハイライト
     */
    codeHighlight: function() {
        prettyPrint();
    },
    
    /**
     * BBコードをHTMLに変換する
     * @param String 変換対象文字列
     * @return String 変換後の文字列
     */
    bb2html: function(entry) {
        var tags = getCommentTags(edButtons);
        return getStringCommentTags(tags, entry);
    },
    
    /**
     * ブラウザ表示用に変換する
     * @param String 変換対象文字列
     * @param Array 変換記号
     * @return String 変換後文字列
     */
    toEntryReference: function(str, optTargets) {
        var target = typeof optTargets !== "undefined" ?
            optTargets : ['\n', '&', '<', '>', '\''];
        for (var i = 0; i < target.length; i++) {
            var cond = target[i];
            //&amp;
            if (cond == "&") 
                str = str.split('&').join('&amp;');
            //&lt;
            if (cond == "<") 
                str = str.split('<').join('&lt;');
            //&gt;
            if (cond == "\'") 
                str = str.split('>').join('&gt;');
            //&quot;
            if (cond == "\n") 
                str = str.split('\'').join('&quot;');
        }
        // 改行コードだけはHTMLタグにする
        str = str.split('\n').join('<br/>');
        
        return str;
    },
    
    /**
     * 配列の中に要素が含まれるかどうか検出する
     * @param {*} elem 要素
     * @param {Array} array 検索対象の配列
     * @returns {Number} キー番号
     */
    inArray: function(elem, array) {
        for (var i = 0, len = array.length; i < len; i++) {
            if (array[i] === elem) {
                return i;
            }
        }
        return -1;
    }
});

/**
 * QuickTagsPlus拡張モジュール
 */
Mixjs.interface(D.Common).module("QuickTagsExtension", D, {
    /**
     * 登録済み画像一覧画面を開く
     */
    onLoadUploadImage: function() {
        var targetURL = this._root + "/upload_image_register?__CSRF_TOKEN__=" + this.getCsrfToken();
        window.open(targetURL,
                    "upload_image",
                    "width=1200 height=700");
    },
    
    /**
     * Amazon商品検索を開く
     */
    onLoadAmazonSearch: function() {
        var targetURL = this._root + "/amazon_search?__CSRF_TOKEN__=" + this.getCsrfToken();
        window.open(targetURL,
                    "amazon_search",
                    "width=700 height=410");
    }
});

/**
 * PostMessageモジュール
 */
Mixjs.module("PostMessage", D, {
    /** 依存モジュール */
    include: D.Util,
    
    /** ターゲットウィンドウ */
    targetWindow: null,
    
    /**
     * メッセージをプレビュー画面に送信する
     * @param Hash 送信データ
     */
    postMessage: function(data) {
        $.postMessage(data, this._host, this.targetWindow);
    },
    
    /**
     * メッセージを受信しプレビュー画面に反映する
     * @param Function メッセージ受信後に実行する関数
     */
    receiveMessage: function(callback) {
        var self = this;
        $.receiveMessage(function(e) {
            var data = e.data.split("&"), obj = {};
            for (var i = 0; i < data.length; i++) {
                var d = data[i].split("=");
                obj[d[0]] = decodeURIComponent(d[1].replace(/\+/g, " "));
            }
            callback.call(self, obj);
        });
    }
});

/**
 * エントリ登録関連モジュール
 */
Mixjs.interface(D.Common).module("EntryRegister", D, {
    /** 依存モジュール */
    include: [D.PostMessage, D.Util, Http],
    
    /**
     * 初期化処理
     */
    init: function() {
        var self = this;
        this.selectedTags = [];
        $("#preview, #image_no_preview").click(function() {
            self.onPreview();
        });
        $("#confirm").click(function() {
            if (self.validate()) {
                for (var i = 0; i < self.selectedTags.length; i++) {
                    // タグIDをformにセットする
                    $("<input></input>").attr({
                        name: "tags[]",
                        type: "hidden"
                    })
                    .val(self.selectedTags[i])
                    .appendTo($("form"));
                    // タグ名をformにセットする(確認画面でのみ使用)
                    var tagId = "tag_" + self.selectedTags[i];
                    $("<input></input>").attr({
                        name: "tag_names[]",
                        type: "hidden"
                    })
                    .val($("li#" + tagId + " span").text())
                    .appendTo($("form"));
                }
            }
            else {
                $("#caution").show();
                return false;
            }
        });
        $("#temporary_save").click(function() {
            self.temporarySave();
        });
        $("#temporary_load").click(function() {
            self.temporaryLoad();
        });
        return this;
    },
    
    /**
     * エントリ内容一時保存
     */
    temporarySave: function() {
        var self = this;
        var params = {
            "title": $("#entry_title").val(),
            "description": $("#entry_textarea").val(),
            "tags": this.selectedTags
        };
        this.xhr({
            url: this._root + "/temporary_save",
            args: {type: "post"},
            params: params,
            success: function(res) {
                if (res.result) {
                    $("#temporary_result").html("エントリ内容を一時保存しました。");
                }
            }
        });
    },
    
    /**
     * エントリ内容一時保存内容取得
     */
    temporaryLoad: function() {
        var self = this;
        this.xhr({
           url: this._root + "/temporary_load",
           args: {type: "get", dataType: "json"},
           success: function(res) {
               if (res) {
                   // 選択済みタグを一旦解除
                   $("span.selected_tag").each(function() {
                       $(this).trigger("click");
                   });
                   if (res.tags !== null) for (var i = 0; i < res.tags.length; i++) {
                       // 保存してあったタグを選択
                       if (self.inArray(res.tags[i], self.selectedTags)) {
                           var tagId = "tag_" + res.tags[i];
                           $("li#" + tagId).trigger("click");
                       }
                   }
                   $("#entry_title").val(res.title);
                   $("#entry_textarea").val(res.description);
                   $("#temporary_result").html("一時保存エントリ内容を読み込みました。");
               }
           },
           after: function() {
               self.postMessage(self.getPreviewData());
           }
        });
    },
    
    /**
     * タグ一覧表示処理
     * @param Function 任意のコールバック
     */
    tag: function(optCallback) {
        var self = this;
        self.parent.list(function() {
            $("div.tag_list li").each(function() {
                $(this).css("cursor", "pointer");
                $(this).toggle(function() {
                    var tagId = $(this).attr("id").replace("tag_", "");
                    $(this).find("span").addClass("selected_tag");
                    self.selectedTags.push(tagId);
                    self.postMessage(self.getPreviewData());
                }, function() {
                    var tagId = $(this).attr("id").replace("tag_", "");
                    $(this).find("span").removeClass("selected_tag");
                    var idx = self.selectedTags.indexOf(tagId);
                    self.selectedTags.splice(idx, 1);
                    self.postMessage(self.getPreviewData());
                });
            });
            optCallback.call(self);
        });
    },
    
    /**
     * 警告メッセージを表示する
     */
    caution: function() {
        $("#caution_close").click(function() {
            $("#caution").hide();
            $("#entry_title").removeClass("warning");
            $(".tag_list").removeClass("warning");
            $("#entry_textarea").removeClass("warning");
        });
    },
    
    /**
     * プレビューを表示する
     */
    displayPreview: function() {
        var self = this;
        this.receiveMessage(function(data) {
            $("#title_preview").html(data["title"]);
            $("#text_preview").html(self.bb2html(data["text"]));
            $("#tag_preview").html(data["tag"]);
            self.fancybox();
            self.codeHighlight();
            $("a.fancybox").each(function() {
                if (/(.*)small\/(.*)/.test(this.href)) {
                    var href = RegExp.$1 + RegExp.$2;
                    $(this).attr("href", href);
                }
            });
        });
    },
    
    /**
     * プレビューに使用するデータを取得する
     * @return Hash プレビューデータ
     */
    getPreviewData: function() {
        var title = this.toEntryReference($("#entry_title").val()),
            text = this.toEntryReference($("#entry_textarea").val()),
            tag = "";
        
        var tags = [];    
        for (var i = 0; i < this.selectedTags.length; i++) {
            var tagId = "tag_" + this.selectedTags[i];
            tags.push($("li#" + tagId + " span").text());
        }
        tag = tags.join(" ");
            
        if ($("#image_no_preview").is(":checked")) {
            text = noImagePreview(text);
        }
        var data = {
            title: title || "(タイトルを入力してください)",
            text: text || "(本文を入力してください)",
            tag: tag || "(タグ未設定)"
        };
        return data;
    },
    
    /**
     * プレビューを実行する
     */
    onPreview: function() {
        var self = this,
            data = self.getPreviewData();
        if (this.targetWindow === null) {
            var targetURL = this._root + "/entry_register_preview?__CSRF_TOKEN__=" + this.getCsrfToken();
            this.targetWindow = window.open(targetURL,
                                            "preview",
                                            "width=1200 height=700");
            setTimeout(function() { self.postMessage(data); }, 1000);
            return;
        }
        this.postMessage(data);
    },
    
    /**
     * リアルタイムプレビューを有効にする
     */
    onRealtimePreview: function() {
        var self = this;
        $("#entry_textarea, #entry_title").keyup(function(event) {
            if ($("#realtime").is(":checked")) {
                self.postMessage(self.getPreviewData());
            }
        });
    },
    
    /**
     * 入力チェック
     */
    validate: function() {
        var isValid = true;
        $("#caution_message").empty();
        
        // タイトル
        if ($("#entry_title").val() == '') {
            $("#caution_message").get(0).innerHTML += "<p>タイトルが入力されていません。</p>";
            $("#entry_title").addClass("warning");
            isValid = false;
        }
        // タグ
        if (this.selectedTags.length === 0) {
            $("#caution_message").get(0).innerHTML += "<p>タグが選択されていません。</p>";
            $(".tag_list").addClass("warning");
            isValid = false;
        }
        // 本文
        if ($("#entry_textarea").val() == '') {
            $("#caution_message").get(0).innerHTML += "<p>本文が入力されていません。</p>";
            $("#entry_textarea").addClass("warning");
            isValid = false;
        }
        
        return isValid;
    },
    
    /**
     * 差し戻しエントリデータをロードする
     * @param Object エントリデータ
     */
    remandLoad: function(entry) {
        var self = this;
        for (var i = 0; i < entry.tags.length; i++) {
            var tagId = "tag_" + entry.tags[i];
            $("li#" + tagId).trigger("click");
        }
        $("#entry_title").val(entry.title);
        $("#entry_textarea").val(entry.description);
    }
});

/**
 * エントリ確認関連モジュール
 */
Mixjs.interface(D.Common).module("EntryConfirm", D, {
    /** 依存モジュール */
    include: D.Util,
    
    /**
     * 初期処理
     */
    init: function() {
        this.remand();
        return this;
    },
    
    /**
     * エントリ内容差し戻し
     */
    remand: function() {
        $("#remand").click(function() {
            $("form").attr("action", "/diarysys5/remand");
        });
    }
});

/**
 * タグ登録関連モジュール
 */
Mixjs.interface(D.Common).module("Tag", D, {
    /** 依存モジュール */
    include: Http,
    
    /**
     * 初期処理
     */
    init: function() {
        var self = this;
        this.token = $("form").find("input[name=__CSRF_TOKEN__]").val();
        $("#add_tag").click(function() {
            $("#success_message, #failure_message").empty();
            self.add($("#new_tag").val());
        });
        $("#del_tag").click(function() {
            $("#success_message, #failure_message").empty();
            self.delete();
        });
        this.list();
        
        return this;
    },
    
    /**
     * タグ一覧を表示
     * @param Function コールバック関数
     */
    list: function(optCallback) {
        var self = this;
        this.xhr({
            url: this._root + "/tag_list",
            args: {type: "get", dataType: "html"},
            params: {"__CSRF_TOKEN__": self.getCsrfToken()},
            success: function(res) {
                $("#tag_list").html(res);
                // タグクラウド化
                $("#tag_list li").each(function() {
                    var count = $(this).find("sup").html();
                    var className = self.tagCloud(count);
                    $(this).find("span, sup").addClass(className);
                });
                // コピーする
                $("#tag_list_org").html($("#tag_list").html());
            },
            after: function() {
                if (typeof optCallback === "function") {
                    optCallback.call(self);
                }
            }
        });
    },
    
    /**
     * タグを登録する
     * @param String タグ名
     */
    add: function(tag) {
        var self = this;
        this.xhr({
            url: this._root + "/tag_register/" + tag,
            args: {type: "post", dataType: "json"},
            params: {"__CSRF_TOKEN__": self.token},
            success: function(res) {
                if (res.status === 'success') {
                    $("#success_message").html(res.message);
                    self.list();
                }
                else {
                    $("#failure_message").html(res.message);
                }
            },
            error: function(res) {
                $("#add_tag_message").html(res);
            }
        });
    },
    
    /**
     * タグを削除する
     */
    delete: function() {
        var self = this;
        if (window.confirm("使われていないタグを全て削除しますか？")) {
            this.xhr({
                url: this._root + "/tag_delete",
                args: {type: "post", dataType: "json"},
                params: {"__CSRF_TOKEN__": self.token},
                success: function(res) {
                    if (res.status === 'success') {
                        $("#delete_result").html(res.message);
                        self.list();
                    }
                    else {
                        alert("error!");
                    }
                }
            });
        }
    },
    
    /**
     * タグをインクリメンタル検索する
     */
    search: function() {
        $("#tag_search").keyup(function() {
            $("#tag_list").html($("#tag_list_org").html());
            var word = $(this).val();
            if (word !== "") {
                $("#tag_list li span").each(function() {
                    var tag = $(this).html();
                    var re = new RegExp("^" + word, 'i');
                    var rep = tag.replace(re, "<mark>"+word+"</mark>");
                    $(this).html(rep);
                });
            }
        });
    }
});

/**
 * アップロード済み画像一覧を取得するモジュール
 */
Mixjs.interface(D.Common).module("ImageList", D, {
    /** 依存モジュール */
    include: [Http, D.Util],
    
    /**
     * 画像ロード開始
     * @param Integer ページ番号
     */
    init: function(page) {
        var self = this;
        var url = "/diarysys5/js/lib/LoadManager.js";
        this.loadScript(url, function() {
            self.xhr(page);
        });
        return this;
    },
    
    /**
     * 画像を表示する
     * @param Array 画像一覧
     * @param Function 実行完了時のコールバック
     */
    setImage: function(list, completeCallback) {
        var self = this;
        var manager = new LoadManager();
        var len = list.length;
        for (var i = 0; i < len; i++) {
            $("#img_container").append('<div id="thumb_' + i + '" class="thumb"></div>');
        }
        for (var j = 0; j < len; j++) {
            manager.add("/diarysys5/img/upload/thumbnail/" + list[j], {
                id: j, src: "/diarysys5/img/upload/" + list[j]
            });
        }
        
        var loadedList = [];
        manager.onProgress = function(event) {
            var img = event.data.image,
                id = event.data.extra.id,
                orgImgSrc = event.data.extra.src;
            
            if (self.inArray(orgImgSrc, loadedList)) {
                loadedList.push(orgImgSrc);
                img.width = img.height = 128;
                var a = $("<a></a>").addClass("fancybox").attr("href", orgImgSrc).append(img).fancybox();
                $("#thumb_" + id).append(a).css({"background-image":"none"});
                $(img).css({opacity:0}).animate({opacity:1}, 300);
            }
        };
        manager.onComplete = completeCallback;
        manager.start();
    },
    
    /**
     * 表示データを取得する
     * @param Integer ページ番号
     */
    xhr: function(page) {
        var self = this;
        this.parent.xhr({
            url: this._root + "/image_list/" + page,
            args: {type: "get", dataType: "json"},
            success: function(res) {
                self.setImage(res);
            }
        });
    }
});

/**
 * 登録済み画像一覧を取得する
 */
Mixjs.module("RegisteredImageList", D, {
    /** 依存モジュール */
    include: D.ImageList,
    
    /**
     * テキストエリアに画像を追加する
     */
    toTextArea: function() {
        var self = this;
        $("a.fancybox").each(function() {
            $(this).click(function(e) {
                var parent = window.opener;
                var src = null;
                if (/(.*\/)(.*)/.test(this.href)) {
                    src = RegExp.$1 + "small/" + RegExp.$2;
                }
                var bbcode = "[upload]" + src + "[/upload]";
                parent.edInsertContent(parent.getCanvasElement(), bbcode);
            });
        });
    },

    /**
     * 画像を表示する
     * @param Array 画像一覧
     * @param Function 実行完了時のコールバック
     */
    setImage: function(list, completeCallback) {
        var self = this;
        var manager = new LoadManager();
        var len = list.length;
        for (var i = 0; i < len; i++) {
            $("#img_container").append('<div id="thumb_' + i + '" class="thumb"></div>');
        }
        for (var j = 0; j < len; j++) {
            manager.add("/diarysys5/img/upload/thumbnail/" + list[j], {
                id: j, src: "/diarysys5/img/upload/" + list[j]
            });
        }
        
        var loadedList = [];
        manager.onProgress = function(event) {
            var img = event.data.image,
                id = event.data.extra.id,
                orgImgSrc = event.data.extra.src;
            
            if (self.inArray(orgImgSrc, loadedList)) {
                loadedList.push(orgImgSrc);
                img.width = img.height = 128;
                var a = $("<a></a>").addClass("fancybox").attr("href", orgImgSrc).append(img);
                $("#thumb_" + id).append(a).css({"background-image":"none"}).click(function() {
                    return false;
                });
                $(img).css({opacity:0}).animate({opacity:1}, 300);
            }
        };
        manager.onComplete = completeCallback;
        manager.start();
    },
    
    /**
     * 表示データを取得する
     * @param Integer ページ番号
     */
    xhr: function(page) {
        var self = this;
        this.parent.parent.xhr({
            url: this._root + "/image_list/" + page,
            args: {type: "get", dataType: "json"},
            success: function(res) {
                self.setImage(res, function() {
                    self.toTextArea();
                });
            }
        });
    }
});

/**
 * Amazon商品検索モジュール
 */
Mixjs.interface(D.Common).module("Amazon", D, {
    /** 依存モジュール */
    include: Http,
    
    /**
     * 初期化処理
     */
    init: function() {
        var self = this;
        $("#search_item").click(function() {
            self.search();
        });
    },
    
    /**
     * 画像選択
     * @param Hash 商品データ
     */
    select: function(item) {
        var parent = window.opener;
        var bbcode = "[amazon=" + item.url + "]" +
                     item.image     + "|" +
                     item.author    + "|" +
                     item.price     + "|" +
                     item.isbn      + "|" +
                     item.publisher + "|" +
                     item.title     + "|" +
                     item.date +
                     "[/amazon]";
        parent.edInsertContent(parent.getCanvasElement(), bbcode);
    },
    
    /**
     * 検索する
     */
    search: function() {
        var self = this,
            keyword = $("#item_keyword").val();
        this.xhr({
            url: this._root + "/amazon_item/" + keyword ,
            params: {"__CSRF_TOKEN__": this.getCsrfToken()},
            args: {type: "get", dataType: "html"},
            success: function(html) {
                $("#item_container").html(html);
            },
            before: function() {
                $("#item_container").css("display", "block");
            },
            after: function() {
                $("div.amazon_item").each(function() {
                    $(this).click(function() {
                        var item = {};
                        $(this).find("div[class='item_info'] > div").each(function() {
                            item[$(this).attr("class")] = $(this).text();
                        });
                        self.select(item);
                    });
                });
            }
        });
    }
});

/**
 * 画面レイアウト関連モジュール
 */
Mixjs.module("PageLayout", D, {
    /** 依存モジュール */
    include: D.Util,
    
    /**
     * プラグインをまとめて起動
     */
    plugin: function() {
        this.fancybox();
        this.codeHighlight();
    },
    
    /**
     * 管理画面：サブヘッダ
     * @param Integer 選択状態のインデックス
     */
    subHeader: function(idx) {
        $("ul.sub_header > li:eq(" + idx + ")").each(function() {
           $(this).addClass("selected");
        });
    },
    
    /**
     * タグクラウド用のクラス名を返却する
     * @param Integer タグ登録数
     * @return String クラス名
     */
    tagCloud: function(count) {
        var size = Math.ceil(count / 10);
        if (size > 10) size = 10;
        return "tagcloud" + size;
    }
});

/**
 * ひとりごと
 */
Mixjs.interface(D.Common).module("Hitorigoto", D, {
    /** 依存モジュール */
    include: Http,
    
    /**
     * 表示データを取得する
     */
    xhr: function() {
        var self = this;
        this.parent.xhr({
            url: this._root + "/hitorigoto",
            args: {type: "get", dataType: "html"},
            success: function(res) {
                $("#hitorigoto").html(res);
            }
        });
    }
});

})(window);