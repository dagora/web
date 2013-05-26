/* Dagora v0.1 - 5/26/2013
   http://
   Copyright (c) 2013 Javier Jimenez Villar - Licensed GPLv3 */
(function(){window.Dagora=function(){var t,e;t=" http://api.dagora.es/v1/";e=function(e,o,n){var r,i=this;if(n==null){n={}}r=new Hope.Promise;TukTuk.Modal.loading();$.ajax({url:t+o,type:e,data:n,dataType:"json",success:function(t){return i._delayPromise(r,null,t)},error:function(t,e,o){return i._delayPromise(r,t,null)}});return r};return{_delayPromise:function(t,e,o){TukTuk.Modal.hide();return setTimeout(function(){return t.done(e,o)},300)},api:e}}()}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__Model.Source=function(t){e(o,t);function o(){return o.__super__.constructor.apply(this,arguments)}o.fields("id","title","link","unit","created","updated");return o}(Monocle.Model)}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__Model.Stat=function(t){e(o,t);function o(){return o.__super__.constructor.apply(this,arguments)}o.fields("id","title","link","unit","unit","data","created","updated");return o}(Monocle.Model)}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__View.GraphBar=function(t){var o;e(n,t);o=void 0;n.prototype.container="section > article #bar";n.prototype.template='<div data-graph="bar">\n  <h4 class="text bold color theme uppercase">PROGRESION EN {{unit}}</h4>\n  <div class="graph"></div>\n</div>';function n(){var t,e,o,r,i,a;n.__super__.constructor.apply(this,arguments);this.html(this.model);t=[["Año",this.model.unit]];a=this.model.data;for(r=0,i=a.length;r<i;r++){o=a[r];t.push([o.date,o.value])}t=google.visualization.arrayToDataTable(t);e={animation:{duration:1e3,easing:"linear"},areaOpacity:.1,backgroundColor:"#ecf0f1",chartArea:{width:"100%",top:20},colors:["#bdc3c7"],fontName:"Oswald",fontSize:12,legend:{position:"none"},pointSize:16,hAxis:{baselineColor:"#f00",textStyle:{color:"#aaa"}},vAxis:{gridlines:{color:"#ecf0f1",count:0}},height:232};this.instance=new google.visualization.ColumnChart(this.el.find(".graph").get(0));this.instance.draw(t,e)}return n}(Monocle.View)}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__View.GraphPie=function(t){var o;e(n,t);o=void 0;n.prototype.container="section > article #pie";n.prototype.template='<li class="margin-top" data-graph="pie">\n  <h4 class="text bold color theme uppercase">{{title}}</h4>\n  <div class="graph"></div>\n</li>';function n(){var t,e;n.__super__.constructor.apply(this,arguments);this.append(this.model);e={colors:["3498db","#ddd"],legend:{position:"none"},fontSize:18,fontName:"Oswald",chartArea:{width:"75%",height:"75%",top:0},tooltip:{trigger:"focus",showColorCode:true}};t=google.visualization.arrayToDataTable([["Value","Value"],[this.model.name,this.model.percent],["Available",100-this.model.percent]]);this.instance=new google.visualization.PieChart(this.el.find(".graph").get(0));this.instance.draw(t,e)}return n}(Monocle.View)}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__View.SourceListItem=function(t){e(o,t);o.prototype.container="[data-context=search] > table";o.prototype.template='<tr>\n  <td class="padding">\n    <h5 class="text color theme">{{title}}</h5>\n    <small class="text book">{{link}}</small>\n  </td>\n  <td class="padding text align right book">\n    <span class="icon calendar"></span> {{created}}\n  </td>\n</tr>';o.prototype.events={click:"onClick"};function o(){o.__super__.constructor.apply(this,arguments);this.append(this.model)}o.prototype.onClick=function(t){return this.url(""+window.location.hash+"/"+this.model.id)};return o}(Monocle.View)}).call(this);(function(){var t={}.hasOwnProperty,e=function(e,o){for(var n in o){if(t.call(o,n))e[n]=o[n]}function r(){this.constructor=e}r.prototype=o.prototype;e.prototype=new r;e.__super__=o.prototype;return e};__View.SourceOverview=function(t){var o;e(n,t);o=void 0;n.prototype.container="section > article #overview";n.prototype.template='<h4 class="text bold uppercase">{{title}}</h4>\n<ul class="margin" data-tuktuk="totals" id="overview">\n    <li>\n        <span class="icon book"></span>\n        <strong>{{data.length}}</strong>\n        <small>registros</small></li>\n    <li>\n        <strong>{{unit}}</strong>\n        <small>unidad</small>\n    </li>\n    <li>\n        <span class="icon dashboard"></span>\n        <strong>{{progresion}}%</strong>\n        <small>progresion</small>\n    </li>\n</ul>';function n(){var t;n.__super__.constructor.apply(this,arguments);t=this.model.data[this.model.data.length-1].value*100/this.model.data[0].value;this.model.progresion=parseInt(t);this.html(this.model)}return n}(Monocle.View)}).call(this);(function(){var t,e={}.hasOwnProperty,o=function(t,o){for(var n in o){if(e.call(o,n))t[n]=o[n]}function r(){this.constructor=t}r.prototype=o.prototype;t.prototype=new r;t.__super__=o.prototype;return t};t=function(t){o(e,t);function e(){return e.__super__.constructor.apply(this,arguments)}e.prototype.elements={"#title":"txtTitle","#link":"txtLink","#unit":"txtUnit","#data":"txtData"};e.prototype.events={"click [data-action=add]":"onAdd"};e.prototype.onAdd=function(t){var e;e={title:this.txtTitle.val(),link:this.txtLink.val(),unit:this.txtUnit.val(),data:this.txtData.val()};return Dagora.api("POST","sources.json",e).then(function(t,e){if(e){return TukTuk.Modal.show("source_added")}})};return e}(Monocle.Controller);__Controller.Add=new t("[data-tuktuk=modal]#add_source")}).call(this);(function(){var t,e={}.hasOwnProperty,o=function(t,o){for(var n in o){if(e.call(o,n))t[n]=o[n]}function r(){this.constructor=t}r.prototype=o.prototype;t.prototype=new r;t.__super__=o.prototype;return t};t=function(t){o(e,t);e.prototype.elements={"input#txt-search":"txtSearch","[data-context=search] > table":"results"};e.prototype.events={"click header button":"search","keypress input":"onSearch"};function e(){e.__super__.constructor.apply(this,arguments);__Model.Source.bind("create",this.bindSourceCreated)}e.prototype.bindSourceCreated=function(t){return new __View.SourceListItem({model:t})};e.prototype.onSearch=function(t){__Controller.Source.hide();if(t.keyCode===13){return this.search()}};e.prototype.fetch=function(t){var e=this;this.results.html("");return Dagora.api("GET","sources.json",{s:t}).then(function(t,e){var o,n,r,i,a;if(e!=null){i=e.data.resultsList;a=[];for(n=0,r=i.length;n<r;n++){o=i[n];a.push(__Model.Source.create(o))}return a}else{return alert("Algo ha ido mal")}})};e.prototype.search=function(){if(this.txtSearch.val()){__Controller.Source.hide();return this.url(this.txtSearch.val())}};return e}(Monocle.Controller);__Controller.Search=new t("section")}).call(this);(function(){var t,e=this,o={}.hasOwnProperty,n=function(t,e){for(var n in e){if(o.call(e,n))t[n]=e[n]}function r(){this.constructor=t}r.prototype=e.prototype;t.prototype=new r;t.__super__=e.prototype;return t};t=function(t){n(e,t);e.prototype.elements={".box":"boxes","#pie":"pies","#updated":"updated"};function e(){var t=this;this.bindStatCreated=function(o){return e.prototype.bindStatCreated.apply(t,arguments)};e.__super__.constructor.apply(this,arguments);__Model.Stat.bind("create",this.bindStatCreated)}e.prototype.bindStatCreated=function(t){this.pies.html("");this.updated.html(t.updated);new __View.SourceOverview({model:t});new __View.GraphBar({model:t});new __View.GraphPie({model:{title:"Dato 1",percent:25}});new __View.GraphPie({model:{title:"Dato 2",percent:75}});new __View.GraphPie({model:{title:"Dato 3",percent:34}});new __View.GraphPie({model:{title:"Dato 4",percent:17}});return this.boxes.addClass("active")};e.prototype.fetch=function(t){var e=this;__Model.Stat.destroyAll();return Dagora.api("GET","sources/"+t+".json").then(function(t,e){if(e!=null){return __Model.Stat.create(e.data)}})};e.prototype.hide=function(){return this.boxes.removeClass("active")};return e}(Monocle.Controller);__Controller.Source=new t("section article[data-context=source]")}).call(this);(function(){var t,e={}.hasOwnProperty,o=function(t,o){for(var n in o){if(e.call(o,n))t[n]=o[n]}function r(){this.constructor=t}r.prototype=o.prototype;t.prototype=new r;t.__super__=o.prototype;return t};t=function(t){o(e,t);e.prototype.elements={"[data-context]":"context"};function e(){e.__super__.constructor.apply(this,arguments);this.routes({":context/:id":this.source,":context":this.search});Monocle.Route.listen();if(!window.location.hash){this.search(null)}}e.prototype.search=function(t){this._context("search");if((t!=null?t.context:void 0)!=null&&t.context!==""){return __Controller.Search.fetch(t.context)}};e.prototype.source=function(t){this._context("source");if(t){return __Controller.Source.fetch(t.id)}};e.prototype._context=function(t){return this.context.hide().siblings("[data-context="+t+"]").show()};return e}(Monocle.Controller);$(function(){return __Controller.Url=new t("body")})}).call(this);