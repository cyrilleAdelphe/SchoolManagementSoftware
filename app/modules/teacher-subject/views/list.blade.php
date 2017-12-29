@extends('backend.'.$role.'.main')

@section('content')
      <!-- =============================================== -->

          
                <div class="nav-tabs-custom">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><i class="fa fa-fw fa-list"></i> Subjects list</a></li>
                    <li><a href="#tab_2" data-toggle="tab"><i class="fa fa-fw fa-edit"></i> Add New</a></li>
                  </ul>
                  <div class="tab-content">                    
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="form-group">
                              <label>Select class</label>
                              <select class="form-control">
                                <option>option 1</option>
                                <option>option 2</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="form-group">
                              <label>Select section</label>
                              <select class="form-control">
                                <option>option 1</option>
                                <option>option 2</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <h4 class="text-red">Class Five A <small>Last updated by : Name at <span class="text-green">12 March 2016</span></small></h4>
                      <table id="pageList" class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>SN</th>
                            <th>Name</th>
                            <th>Subject type</th>
                            <th>Teacher</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>English</td>
                            <td>Theory</td>
                            <td>Shyam Sharma</td>
                            <td>
                              <button data-toggle="tooltip" title="Edit" class="btn btn-info" type="button">
                                <i class="fa fa-fw fa-edit"></i>
                              </button>
                              <button data-toggle="tooltip" title="Delete" class="btn btn-danger" type="button">
                                <i class="fa fa-fw fa-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div><!-- tab 1 ends -->
                    <div class="tab-pane" id="tab_2">
                      <div class="form-group ">
                        <label>Name</label>
                        {{HelperController::generateSelectList('Subject', 'subject_name', 'id', 'subject_id', $selected = Input::old('subject_id'), $condition = array())}}
                      </div>
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Select class</label>
                            <select class="form-control">
                              <option>option 1</option>
                              <option>option 2</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Select section</label>
                            <select class="form-control">
                              <option>option 1</option>
                              <option>option 2</option>
                            </select>
                          </div>
                        </div>
                      </div><!-- row ends -->
                      <div class="form-group">
                        <label>Subject Type</label>
                        <select class="form-control">
                          <option>Theory</option>
                          <option>Theory and Practical</option>
                        </select>
                      </div>
                      <div class="form-group ">
                        <label>Teacher</label>
                        <select class="form-control">
                          <option>option 1</option>
                          <option>option 2</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <button class="btn btn-primary" type="submit">Submit</button>
                      </div>
                    </div><!-- tab 2 ends -->
                  </div>
                </div>
              

@stop