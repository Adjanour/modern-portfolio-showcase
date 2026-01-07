jQuery(document).ready(function ($) {
  let selectedImages = [];
  let mediaUploader;

  // Initialize based on current page
  const page = getUrlParameter("page");

  if (page === "modern-portfolio-categories") {
    initializeCategories();
  } else if (page === "modern-portfolio-projects") {
    initializeProjects();
  }

  // === CATEGORY MANAGEMENT ===
  function initializeCategories() {
    // Auto-generate slug from category name
    $("#category-name").on("input", function () {
      const slug = generateSlug($(this).val());
      $("#category-slug").val(slug);
    });

    // Save category
    $("#category-form").on("submit", function (e) {
      e.preventDefault();

      const categoryId = $("#category-id").val();
      const name = $("#category-name").val().trim();
      const slug = $("#category-slug").val().trim();

      if (!name || !slug) {
        alert("Category name and slug are required");
        return;
      }

      const data = {
        action: "save_category",
        nonce: portfolioAjax.nonce,
        category_id: categoryId,
        name: name,
        slug: slug,
      };

      $.post(portfolioAjax.ajax_url, data, function (response) {
        if (response.success) {
          alert(response.data.message);
          resetCategoryForm();
          location.reload();
        } else {
          alert("Error: " + response.data);
        }
      });
    });

    // Edit category
    $(document).on("click", ".edit-category", function () {
      const categoryId = $(this).data("id");
      const row = $(this).closest("tr");
      const name = row.find("td:eq(0)").text();
      const slug = row.find("td:eq(1)").text();

      $("#category-id").val(categoryId);
      $("#category-name").val(name);
      $("#category-slug").val(slug);

      $(".cancel-category-edit").show();
      $("html, body").animate(
        {
          scrollTop: $("#category-form").offset().top - 50,
        },
        500
      );
    });

    // Delete category
    $(document).on("click", ".delete-category", function () {
      if (
        !confirm(
          "Are you sure? Projects in this category will be uncategorized."
        )
      ) {
        return;
      }

      const categoryId = $(this).data("id");

      $.post(
        portfolioAjax.ajax_url,
        {
          action: "delete_category",
          nonce: portfolioAjax.nonce,
          category_id: categoryId,
        },
        function (response) {
          if (response.success) {
            alert("Category deleted successfully!");
            location.reload();
          } else {
            alert("Error deleting category");
          }
        }
      );
    });

    // Cancel edit
    $(".cancel-category-edit").on("click", function () {
      resetCategoryForm();
    });
  }

  function resetCategoryForm() {
    $("#category-form")[0].reset();
    $("#category-id").val("");
    $("#category-slug").val("");
    $(".cancel-category-edit").hide();
  }

  // === PROJECT MANAGEMENT ===
  function initializeProjects() {
    const editId = getUrlParameter("edit");

    if (editId) {
      // Edit page
      setupProjectImageUpload(
        ".upload-project-images-btn",
        "#project-images-preview",
        "#project-images"
      );
      setupVideoUpload();
      setupProjectForm();
    }
  }

  // Video upload handler
  function setupVideoUpload() {
    let videoUploader;

    $(".upload-video-btn").on("click", function (e) {
      e.preventDefault();

      if (videoUploader) {
        videoUploader.open();
        return;
      }

      videoUploader = wp.media({
        title: "Select or Upload Video",
        button: {
          text: "Use this video",
        },
        library: {
          type: ["video"],
        },
        multiple: false,
      });

      videoUploader.on("select", function () {
        const attachment = videoUploader.state().get("selection").first().toJSON();
        $("#project-video").val(attachment.url);
        $("#video-preview").html('<span class="video-selected">✓ ' + attachment.filename + '</span>');
      });

      videoUploader.open();
    });

    // Update preview when URL is manually entered
    $("#project-video").on("input", function () {
      const url = $(this).val().trim();
      if (url) {
        $("#video-preview").html('<span class="video-selected">✓ Video URL set</span>');
      } else {
        $("#video-preview").empty();
      }
    });
  }

  function setupProjectImageUpload(uploadBtn, previewId, imageInputId) {
    let mediaUploader;

    $(uploadBtn).on("click", function (e) {
      e.preventDefault();

      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      mediaUploader = wp.media({
        title: "Select Project Images",
        button: {
          text: "Use these images",
        },
        multiple: true,
      });

      mediaUploader.on("select", function () {
        const selection = mediaUploader.state().get("selection");
        const images = [];

        selection.each(function (attachment) {
          attachment = attachment.toJSON();
          images.push(attachment.url);
        });

        displayProjectImagePreviews(images, previewId, imageInputId);
      });

      mediaUploader.open();
    });
  }

  function displayProjectImagePreviews(images, previewId, imageInputId) {
    const previewContainer = $(previewId);
    previewContainer.empty();

    images.forEach(function (url, index) {
      const imgWrapper = $(
        '<div class="image-preview-item ' +
          (index === 0 ? "cover-image" : "") +
          '" data-url="' +
          url +
          '"></div>'
      );
      const img = $('<img src="' + url + '" alt="preview">');

      if (index === 0) {
        imgWrapper.append('<span class="cover-badge">Cover Image</span>');
      }

      const removeBtn = $(
        '<button type="button" class="remove-image" data-index="' +
          index +
          '">×</button>'
      );

      imgWrapper.append(img).append(removeBtn);
      previewContainer.append(imgWrapper);
    });

    updateImageInput(imageInputId);

    // Handle remove image clicks
    $(previewId)
      .find(".remove-image")
      .on("click", function (e) {
        e.preventDefault();
        $(this).closest(".image-preview-item").remove();
        updateImageInput(imageInputId);
      });
  }

  function updateImageInput(imageInputId) {
    const urls = [];
    $(imageInputId)
      .closest("td")
      .find(".image-preview-item")
      .each(function () {
        urls.push($(this).data("url"));
      });
    $(imageInputId).val(urls.join(","));
  }

  function setupProjectForm() {
    $("#project-edit-form").on("submit", function (e) {
      e.preventDefault();

      const projectId = $("#project-id").val();
      const title = $("#project-title").val().trim();

      // Get content from TinyMCE editor if it exists
      let description = "";
      if (
        typeof tinymce !== "undefined" &&
        tinymce.get("project-description")
      ) {
        description = tinymce.get("project-description").getContent();
      } else {
        // Fallback to textarea if TinyMCE is not available
        const descElement = document.getElementById("project-description");
        description = descElement ? descElement.value : "";
      }

      const categoryId = $("#project-category").val();
      const projectLink = $("#project-link").val().trim();
      const videoUrl = $("#project-video").val().trim();
      const images = $("#project-images").val();

      // Debug output
      console.log("Form Data:", {
        projectId,
        title,
        description: description.substring(0, 50) + "...",
        categoryId,
        projectLink,
        images: images ? images.substring(0, 50) + "..." : "NO IMAGES",
      });

      if (!title) {
        alert("Project title is required");
        return;
      }

      if (!description) {
        alert("Project description is required");
        return;
      }

      if (!projectLink) {
        alert("Project link is required");
        return;
      }

      if (!images) {
        alert("Please upload at least one image");
        return;
      }

      const data = {
        action: "save_portfolio_item",
        nonce: portfolioAjax.nonce,
        project_id: projectId || 0,
        title: title,
        description: description,
        category_id: categoryId,
        project_link: projectLink,
        video_url: videoUrl,
        images: images,
      };

      $.post(portfolioAjax.ajax_url, data, function (response) {
        console.log("Response:", response);
        if (response.success) {
          alert(response.data.message);
          window.location.href = response.data.redirect;
        } else {
          // Handle both string and object error formats
          const errorMessage =
            typeof response.data === "string"
              ? response.data
              : response.data.message || "Unknown error";
          alert("Error: " + errorMessage);
        }
      });
    });
  }

  // Project deletion from list
  $(document).on("click", ".delete-project-btn", function () {
    if (!confirm("Are you sure you want to delete this project?")) {
      return;
    }

    const projectId = $(this).data("id");

    $.post(
      portfolioAjax.ajax_url,
      {
        action: "delete_portfolio_item",
        nonce: portfolioAjax.nonce,
        project_id: projectId,
      },
      function (response) {
        if (response.success) {
          alert("Project deleted successfully!");
          location.reload();
        } else {
          alert("Error deleting project");
        }
      }
    );
  });

  // === UTILITY FUNCTIONS ===
  function generateSlug(text) {
    return text
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, "")
      .replace(/[\s_-]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  function getUrlParameter(name) {
    const url = new URL(window.location);
    return url.searchParams.get(name);
  }
});
