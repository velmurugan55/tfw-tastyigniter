---
title: 'main::lang.contact.title'
layout: default
permalink: /contact
'[contact]': null
description: ''
---
<div class="container">
    <div class="row">
        <div class="col-md-6 m-auto">
            <div class="card mb-3">
                @if ($contactLocation = $contact->location)
                    <div class="card-body">
                        <h1 class="h3 card-title">{{ $contactLocation->getName() }}</h1>
                        <div class="row contact-info mb-2">
                            <div class="col-1"><i class="fa fa-globe"></i></div>
                          <div class="col">
                                Plot No. 66, 26, Anna St,<br/>
                                Lakshmipuram,<br/>
                                Radhakrishnan Nagar,<br/>
                                Thiruvanmiyur,<br/>
                                Chennai - 600041, India<br/>
                            </div>
                        </div>
                        <div class="row contact-info">
                            <div class="col-1"><i class="fa fa-phone"></i></div>
                           <div class="col">+91 6382096528</div> 
                        </div>
                      <div class="row contact-info">
                            <div class="col-1"><i class="fa fa-envelope"></i></div>
                            <div class="col">support@touchfreewaiter.com</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="contact-title mb-3">@lang('igniter.frontend::default.contact.text_summary')</h4>
                    @component('contact')
                </div>
            </div>
        </div>
    </div>
</div>
