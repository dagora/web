window.Dagora = do ->

  SERVICE   = " http://api.dagora.es/v1/"

  api = (type, method, parameters = {}) ->
    promise = new Hope.Promise()

    do TukTuk.Modal.loading
    $.ajax
      url: SERVICE + method
      type: type
      data: parameters
      dataType: 'json'
      success: (response) =>
        @_delayPromise promise, null, response
      error: (xhr, type, request) =>
        @_delayPromise promise, xhr, null
    promise

  _delayPromise: (promise, error, result) ->
    do TukTuk.Modal.hide
    setTimeout ->
      promise.done error, result
    , 300

  api: api
