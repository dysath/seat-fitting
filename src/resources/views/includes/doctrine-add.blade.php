<form method="POST" action="{{ route('fitting.addDoctrine') }}" id="addDocForm">
    <div class="modal fade" tabindex="-1" role="dialog" id="addDoctrine">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Add a New Doctrine</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                      <label for="doctrinename">Doctrine Name:</label>
                      <input type="text" class="form-control" name="doctrinename" id="doctrinename" />
                    </div>
                    <div class="form-group">
                      <label for="listoffits">Select Fits to Add to Doctrine</label>
                      <select multiple class="form-control" size="6" id="listoffits">
                      </select>
                    </div>
                    <div class="text-center">
                      <div class="btn-group text-center" role="group" style="margin: 0 auto; text-align: center; width: inherit; display: inline-block;">
                        <button type="button" class="btn btn-sm btn-success" id="addFits">Add Fit(s)</button>
                        <button type="button" class="btn btn-sm btn-danger" id="removeFits">Remove Fit(s)</button>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="selectedFits">Chosen Fits</label>
                      <select class="form-control" size="15" id="selectedFits" name="selectedFits[]" multiple="multiple">
                      </select>
                      <input type="hidden" name="doctrineid" id="doctrineid" value="0">
                    </div>
                </div>
                <div class="modal-footer bg-primary">
                    <div class="text-left">
                        <div class="btn-group pull-right" role="group">
                            <button type="submit" class="btn btn-sm btn-success" id="saveDoctrine">Save Doctrine</button>
                            <button type="button" class="btn btn-sm btn-default text-black" data-dismiss="modal" id="Cancel">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
