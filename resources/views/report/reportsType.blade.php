<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2016
 * Time: 2:16 PM
 */?>
@extends('layout.dashbord')

@section('content')
    <div class="content animate-panel">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center m-b-md">
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <table class="table table-hover config-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>REPORT NAME</th>
                                <th>DESCRIPTION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td><a href="/company/list">Company List</a></td>
                                <td>This report allow user to serach company by company name and entity type</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><a href="/company/location/list">Company Location List</a></td>
                                <td>This report allow user to serach company locations by adress and location name</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><a href="/company/users/list">Company Users</a></td>
                                <td>This report allow user to serach users of the companies</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><a href="/inspection/list">Audit Reports</a></td>
                                <td>This report allow user to search audit reports</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
