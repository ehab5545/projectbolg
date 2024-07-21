@extends('layouts.layouts')

@section('content')
<meta name="user-id" content="{{ auth()->user()->id }}">
<style>
    .post-container {
        max-height: 150px;
        overflow: auto;
    }
</style>
<div class="row">
    <div class="col-8">
        <div class="row" id="postadded"></div>
    </div>
    <div class="col-4 mt-5 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>Create New Post</div>
                <button id="createNewPost" class="btn btn-primary">Create</button>
            </div>
            <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <div class="d-flex justify-content-between align-items-center">
                    <button id="showAllPosts" class="btn btn-primary">Show All Posts</button>
                    <button id="showMyPosts" class="btn btn-primary">Show My Posts</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel">Create/Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="postForm" enctype="multipart/form-data">
    <input type="hidden" id="postId" name="id">
    <div class="mb-3">
        <label for="postTitle" class="form-label">Title</label>
        <input type="text" class="form-control" id="postTitle" name="title">
    </div>
    <div class="mb-3">
        <label for="postContent" class="form-label">Content</label>
        <textarea class="form-control" id="postContent" name="content" rows="3"></textarea>
    </div>
    <div class="mb-3">
        <label for="postImage" class="form-label">Image</label>
        <input type="file" class="form-control" id="postImage" name="imgUpload1">
    </div>
    <button type="submit" class="btn btn-primary">Save changes</button>
</form>

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

    let url = '/showposts';
    let userId = $('meta[name="user-id"]').attr('content');

    $('#showAllPosts').on('click', function() {
        url = '/showposts';
        loadPosts(url);
    });

    $('#showMyPosts').on('click', function() {
        url = '/posts/user';
        loadPosts(url);
    });

    $('#searchForm').on('click', function(event) {
        event.preventDefault(); // Prevent the form from submitting in the traditional way
        var searchQuery = $('#filterPosts').val(); // Get the value from the search input
        var searchUrl = '/FilterTitel?query=' + encodeURIComponent(searchQuery); // Append the query to the URL

        loadPosts(searchUrl); // Call loadPosts with the new URL
    });

    function loadPosts(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                let postsHtml = '';
                response.forEach(function(post) {
                    postsHtml += `
                        <div class="card mt-5 col-5 mx-2">
                            <img src="${post.image}" class="card-img-top my-2" alt="${post.title}" style="height: 200px; width: -webkit-fill-available;">
                            <div class="card-body">
                                <h5 class="card-title">${post.title}</h5>
                                <p class="card-text post-container">${post.content}</p>
                                <a href="/posts/create/${post.id}" class="card-text my-2">${post.comments_count} Comments</a>`;
                    if (post.user_id == userId) {
                        postsHtml += `
                            <div class="d-flex justify-content-between align-items-center">                                
                                <a href="#" class="btn btn-info edit-post" data-id="${post.id}">Edit Post</a>
                                <a href="#" class="btn btn-danger delete-post" data-id="${post.id}">Delete Post</a>
                            </div>`;
                    }

                    postsHtml += `</div></div>`;
                });
                $('#postadded').html(postsHtml);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    // Load posts on page load
    loadPosts(url);

    // Handle delete post action
    $(document).on('click', '.delete-post', function(e) {
        e.preventDefault();
        let postId = $(this).data('id');
        $.ajax({
            url: `/delete_posts/${postId}`,
            type: 'DELETE',
            success: function(response) {
                loadPosts(url); // Reload posts after deletion
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });

    // Handle edit post action
    $(document).on('click', '.edit-post', function(e) {
        e.preventDefault();
        let postId = $(this).data('id');
        $.ajax({
            url: `/edit_posts/${postId}`,
            type: 'GET',
            success: function(response) {
                // Populate the modal with the post data
                $('#postId').val(response.id);
                $('#postTitle').val(response.title);
                $('#postContent').val(response.content);
                $('#postImage').val(''); // Clear the file input
                $('#currentPostImage').remove();
                if (response.image) {
                    $('#postImage').after(`<img src="${response.image}" class="img-thumbnail mt-2" alt="${response.title}" id="currentPostImage" style="height: 100px;">`);
                }
                // Show the modal
                $('#postModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    // Handle create new post action
    $('#createNewPost').on('click', function() {
        $('#postForm').trigger('reset');
        $('#postId').val('');
        $('#postModalLabel').text('Create Post');
        $('#currentPostImage').remove();
        $('#postModal').modal('show');
    });

    // Handle form submission
    $('#postForm').submit(function(e) {
        e.preventDefault();
        let postId = $('#postId').val();
        let formData = new FormData(this);
        let method = postId ? 'PUT' : 'POST';
        let url = postId ? `/edit_posts/${postId}` : `/posts`;

        $.ajax({
            url: url,
            type: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#postModal').modal('hide');
                loadPosts('/showposts'); // Reload posts after submission
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    });
});



</script>
