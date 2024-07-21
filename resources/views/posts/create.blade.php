@extends('layouts.layouts')

@section('content')
<meta name="user-id" content="{{ auth()->user()->id }}">
<style>
    .comment-container {
        max-height: 200px;
        overflow: auto;
    }
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col-8 mx-auto">
            <div class="card">
                <img src="" class="card-img-top" id="postImage" alt="Post Image" style="height: 200px;">
                <div class="card-body">
                    <h5 class="card-title" id="postTitle"></h5>
                    <p class="card-text" id="postContent"></p>
                </div>
                <div class="card-footer">
                    <h5>Comments</h5>
                    <div id="comments" class="comment-container"></div>
                    <form id="commentForm" class="mt-3">
                        <input type="hidden" id="postId">
                        <div class="mb-3">
                            <label for="commentContent" class="form-label">Add a Comment</label>
                            <textarea class="form-control" id="commentContent" name="content" rows="2" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let postId = window.location.pathname.split('/').pop();

        // Function to load post details and comments
        console.log(`${postId}`);
        function loadPostDetails() {
            $.ajax({
                url: `/edit_posts/${postId}`,
                type: 'GET',
                success: function(response) {
                    $('#postId').val(response.id);
                    $('#postTitle').text(response.title);
                    $('#postContent').text(response.content);
                    $('#postImage').attr('src', response.image);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
            $.ajax({
                url: `/GetComments/${postId}`,
                type: 'get',
                success: function(response) {
                    let commentsHtml = '';
                    console.log(response)
                    response.forEach(function(comment) {
                        console.log(comment.user)
                        commentsHtml += `
                    
                            <p class="my-0">${comment.user.username}:${comment.content}</p>
                            <hr class="my-0">
                            `;
                    });
                    $('#comments').html(commentsHtml);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
        });
        }

        // Load post details and comments on page load
        loadPostDetails();

        // Handle form submission for adding comments
        $('#commentForm').submit(function(e) {
            e.preventDefault();
            let formData = {
                post_id :  `${postId}` ,
                content: $('#commentContent').val()
            };

            $.ajax({
                url: `/addCommets`,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#commentContent').val(''); // Clear the comment form
                    loadPostDetails(); // Reload post details and comments
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });


    });
</script>
