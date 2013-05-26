class AddSourceCtrl extends Monocle.Controller

  elements:
    "#title"                  : "txtTitle"
    "#link"                   : "txtLink"
    "#unit"                   : "txtUnit"
    "#data"                   : "txtData"

  events:
    "click [data-action=add]" : "onAdd"

  # EVENTS
  onAdd: (event) ->
    parameters =
      title : @txtTitle.val()
      link  : @txtLink.val()
      unit  : @txtUnit.val()
      data  : @txtData.val()

    Dagora.api("POST", "sources.json", parameters).then (error, response) ->
      TukTuk.Modal.show "source_added" if response

__Controller.Add = new AddSourceCtrl "[data-tuktuk=modal]#add_source"
