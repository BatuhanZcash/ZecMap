(function () {
  const drawer = document.getElementById("moderation-review-drawer");
  const dataEl = document.getElementById("moderation-applications-data");
  const approveUrlTemplate = document.getElementById("moderation-approve-url-template");
  const rejectUrlTemplate = document.getElementById("moderation-reject-url-template");
  const approveForm = document.getElementById("moderation-approve-form");
  const rejectForm = document.getElementById("moderation-reject-form");
  const rejectBtn = document.getElementById("moderation-reject-btn");

  if (
    !drawer ||
    !dataEl ||
    !approveUrlTemplate ||
    !rejectUrlTemplate ||
    !approveForm ||
    !rejectForm ||
    !rejectBtn
  ) {
    return;
  }

  const applications = JSON.parse(dataEl.textContent || "[]");
  const applicationMap = new Map(applications.map((item) => [String(item.id), item]));

  const titleEl = document.getElementById("moderation-review-title");
  const nameEl = document.getElementById("moderation-business-name");
  const categoryEl = document.getElementById("moderation-business-category");
  const addressEl = document.getElementById("moderation-business-address");
  const emailEl = document.getElementById("moderation-business-email");
  const ownerEl = document.getElementById("moderation-business-owner");
  const websiteEl = document.getElementById("moderation-business-website");
  const phoneEl = document.getElementById("moderation-business-phone");
  const zcashEl = document.getElementById("moderation-business-zcash");
  const createdAtEl = document.getElementById("moderation-business-created-at");
  const actionsEl = document.getElementById("moderation-review-actions");

  function setText(el, value, fallback) {
    if (!el) {
      return;
    }

    el.textContent = value && String(value).trim() !== "" ? value : fallback || "—";
  }

  // Lazily create the listing image/logo block at the top of the review drawer.
  // Done in JS so no Blade template change is needed.
  let logoWrap = null;
  let logoImg = null;

  function ensureLogoBlock() {
    if (logoWrap) {
      return;
    }
    logoWrap = document.createElement("div");
    logoWrap.id = "moderation-business-logo-wrap";
    logoWrap.style.cssText =
      "display:none;margin:0 0 18px;text-align:center;";
    logoImg = document.createElement("img");
    logoImg.id = "moderation-business-logo";
    logoImg.alt = "Listing image";
    logoImg.style.cssText =
      "max-width:100%;max-height:220px;border-radius:14px;object-fit:cover;" +
      "border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.04);";
    logoWrap.appendChild(logoImg);

    // Insert above the drawer title (or, failing that, at the top of the drawer).
    const anchor =
      (titleEl && titleEl.parentNode) ||
      drawer.querySelector(".moderation-review-body") ||
      drawer;
    if (titleEl && titleEl.parentNode) {
      titleEl.parentNode.insertBefore(logoWrap, titleEl);
    } else {
      anchor.insertBefore(logoWrap, anchor.firstChild);
    }
  }

  function setLogo(url) {
    ensureLogoBlock();
    if (url && String(url).trim() !== "") {
      logoImg.src = url;
      logoWrap.style.display = "block";
    } else {
      logoWrap.style.display = "none";
      logoImg.removeAttribute("src");
    }
  }

  function buildUrl(templateEl, id) {
    return templateEl.dataset.url.replace("__ID__", String(id));
  }

  function openDrawer(applicationId) {
    const item = applicationMap.get(String(applicationId));
    if (!item) {
      return;
    }

    setLogo(item.logo_url);
    setText(titleEl, item.name, "Başvuru");
    setText(nameEl, item.name);
    setText(categoryEl, item.category);
    setText(addressEl, item.address);
    setText(emailEl, item.email);
    setText(ownerEl, item.owner_name);
    setText(websiteEl, item.website);
    setText(phoneEl, item.phone);
    setText(zcashEl, item.zcash_address);
    setText(createdAtEl, item.created_at);

    const isPending = item.is_pending;
    actionsEl.hidden = !isPending;

    approveForm.action = buildUrl(approveUrlTemplate, item.id);
    rejectForm.action = buildUrl(rejectUrlTemplate, item.id);

    drawer.classList.add("is-open");
    drawer.setAttribute("aria-hidden", "false");
  }

  function closeDrawer() {
    drawer.classList.remove("is-open");
    drawer.setAttribute("aria-hidden", "true");
  }

  document.querySelectorAll(".btn-review-application").forEach(function (button) {
    button.addEventListener("click", function () {
      openDrawer(button.dataset.id);
    });
  });

  document.querySelectorAll("[data-close-drawer]").forEach(function (button) {
    button.addEventListener("click", closeDrawer);
  });

  rejectBtn.addEventListener("click", function () {
    rejectForm.submit();
  });
})();
