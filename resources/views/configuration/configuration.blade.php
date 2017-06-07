<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 6/6/2016
 * Time: 10:34 AM
 */
?>

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
                                <th>CONFIGURATION NAME</th>
                                <th>DESCRIPTION</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><a href="/configuration/country">Country Manager</a></td>
                                    <td>Manage the countries that appear in “Country” dropdowns throughout the site.</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><a href="/configuration/state">State Manager</a></td>
                                    <td>Manage the states that appear in “State” dropdowns throughout the site.</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><a href="/configuration/city">City Manager</a></td>
                                    <td>Manage the cities that appear in “City” dropdowns throughout the site.</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td><a href="/configuration/licenses">License Types Manager</a></td>
                                    <td>Manage the different license types for each state that appear in dropdowns throughout the site.</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td><a href="/configuration/mqcategories">Question Main Category Manager</a></td>
                                    <td>Manage the Main Categories for questions (categories that appear on the iPad app).</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td><a href="/configuration/qcategories">Question Categories Manager</a></td>
                                    <td>Manage additional question categories for questions.</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td><a href="/configuration/subscription/mjb">Subscription Plans - MJB</a></td>
                                    <td>Manage the monthly subscription plans for MJBs.</td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td><a href="/configuration/subscription/cc_ge">Subscription Plans - CCs and GEs</a></td>
                                    <td>Manage the monthly subscription plans for CCs and GEs.</td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td><a href="/configuration/userGroup">User Types</a></td>
                                    <td>Manage the names of the different user types.</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td><a href="/configuration/masterdata">Master Data</a></td>
                                    <td>User allows to define global values for the system.</td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td><a href={{URL("/configuration/coupons")}}>Discount Code Manager</a></td>
                                    <td>Manage Discount Codes for MJB subscriptions.</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td><a href={{URL("/configuration/referrals")}}>Referrals Manager</a></td>
                                    <td>Manage referrers for MJB Subscriptions.</td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td><a href={{URL("/configuration/referrals/codes")}}>Referral Code Manager</a></td>
                                    <td>Manage referrals Code for referrers.</td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td><a href={{URL("/configuration/keywords")}}>Keywords Manager</a></td>
                                    <td>Manage keywords.</td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td><a href={{URL("/configuration/applicability")}}>Applicability Manager</a></td>
                                    <td>Manage Applicabilities.</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
