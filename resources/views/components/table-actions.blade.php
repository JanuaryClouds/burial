@props([
    'data' => [],
    'type' => null,
])

<div
    class="row align-items-center g-1 flex-nowrap p-0"
>
    @if ($type === 'service')
        @php
            $contactType = "service";
            $paramKey = 'id';
            $formLink = route('admin.burial.service.email', ['id' => $data->$paramKey]);

            if ($data->representative_email) {
                $email = true;
            } else {
                $email = false;
            }
        @endphp
        <div class="col mt-0" title="View Service">
            <a href="{{ route('admin.burial.view', ['id' => $data->id]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
        <div class="col mt-0" title="Export to PDF">
            <a
                name=""
                id=""
                class="btn btn-secondary"
                href="{{ route('admin.burial.service.print', ['id' => $data->id]) }}"
                role="button"
                target="_blank"
                ><i class="fa-solid fa-print"></i></a
            >
        </div>
    @elseif ($type === 'provider')
        @php
            $contactType = "provider";
            $paramKey = 'id';
            $formLink = route('admin.burial.provider.email', ['id' => $data->$paramKey]);

            if ($data->email) {
                $email = true;
            } else {
                $email = false;
            }
        @endphp
        <div class="col mt-0" title="View Provider">
            <a href="{{ route('admin.burial.provider.view', ['id' => $data->id]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
        <div class="col mt-0" title="Export details to PDF">
            <a
                name=""
                id=""
                class="btn btn-secondary"
                href="{{ route('admin.burial.provider.print', ['id' => $data->id]) }}"
                role="button"
                target="_blank"
                ><i class="fa-solid fa-print"></i></a
            >
        </div>
    @elseif ($type === 'request')
        @php
            $contactType = "requestor";
            $paramKey = 'uuid';
            $formLink = route('admin.burial.request.email', ['uuid' => $data->$paramKey]);

            if ($data->representative_email) {
                $email = true;
            } else {
                $email = false;
            }
        @endphp
        <div class="col mt-0" title="View Request">
            <a href="{{ route('admin.burial.request.view', ['uuid' => $data->uuid]) }}" class="btn btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
        <div class="col mt-0" title="Export details to PDF">
            <a
                name=""
                id=""
                class="btn btn-secondary"
                href="{{ route('admin.burial.request.print', ['uuid' => $data->uuid]) }}"
                role="button"
                target="_blank"
                ><i class="fa-solid fa-print"></i></a
            >
        </div>
    @endif
        @if ($email)
        <div class="col mt-0" title="Contact">
            <!-- Modal trigger button -->
            <button
            type="button"
            class="btn btn-secondary"
            data-bs-toggle="modal"
            data-bs-target="#messageModal-{{ $data->$paramKey }}"
        >
            <i class="fa-solid fa-envelope"></i>
        </button>
        
        <!-- Modal Body -->
        <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
        <div
            class="modal fade"
            id="messageModal-{{ $data->$paramKey }}"
            tabindex="-1"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            
            role="dialog"
            aria-labelledby="modalTitleId-{{ $data->$paramKey }}"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-dialog-scrollable modal-dialog-centered"
                role="document"
                style="z-index: 2"
            >
                <div class="modal-content">
                    <form action="{{ $formLink }}" method="post">
                    @csrf
                    @method('POST')
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">
                                @if ($type == 'service')
                                    Contact {{ $data->representative }}
                                @elseif ($type == 'request')
                                    Contact {{ $data->representative }}
                                @elseif ($type == 'provider')
                                    Contact {{ $data->name }}
                                @endif
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="subject"
                                        id="subject"
                                        aria-describedby="helpId"
                                        placeholder="Subject"
                                    />
                                </div>
                                
                                <p class=text-body>Insert the message below</p>
                                <textarea 
                                    name="message" 
                                    id="message" 
                                    rows="5" 
                                    max="255"
                                    class="form-control"
                                ></textarea>
                            </div>
                            <div class="modal-footer">
                                <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                >
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
