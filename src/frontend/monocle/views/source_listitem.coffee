class __View.SourceListItem extends Monocle.View

  container: "[data-context=search] > table"

  template: """
      <tr>
        <td class="padding">
          <h5 class="text color theme">{{title}}</h5>
          <small class="text book">{{link}}</small>
        </td>
        <td class="padding text align right book">
          <span class="icon calendar"></span> {{created}}
        </td>
      </tr>
    """

  events:
    "click": "onClick"

  constructor: ->
    super
    @append @model


  onClick: (event) ->
    console.error @model
    @url "/" + @model.id
