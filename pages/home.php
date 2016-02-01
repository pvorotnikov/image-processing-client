
<section id="page_<?php echo $page; ?>" class="container">
    <div class="panel panel-primary">

        <div class="panel-heading">
            <h1 class="panel-title">Upload your image</h1>
        </div>

        <div class="panel-body row">

            <div class="col-md-6">
                <form id="imageUploadForm" method="POST" action="ajax/processimage.php?do=classify">

                    <div class="form-group is-empty is-fileinput">
                        <label for="imageFile" class="control-label">Image file</label>
                        <input type="file" id="imageFile" name="imageFile" />
                        <input type="text" readonly="" class="form-control" placeholder="Select your image...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-raised">Send</button>
                </form>

                <div id="imageResult"></div>
            </div>

            <div class="col-md-6 results">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="#" id="classify" class="btn btn-primary btn-sm">Reclassify</a>
                        <span>Classification</span></div>
                    <div class="panel-body" id="classificationResult"></div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="#" id="edge" class="btn btn-primary btn-sm">Detect Edges</a>
                        <span>Edge Detection</span></div>
                    <div class="panel-body" id="edgeResult"></div>
                </div>

            </div>

        </div>

    </div>
</section>
