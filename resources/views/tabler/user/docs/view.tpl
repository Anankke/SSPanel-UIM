{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {$doc->title}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card card-lg">
                <div class="card-body ">
                    <div class="row g-4">
                        {$doc->content}
                    </div>
                </div>
            </div>
        </div>
    </div>

{include file='user/footer.tpl'}