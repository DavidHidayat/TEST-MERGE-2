$(document).ready(function () {
  /**
   * =========================================================================================================
   * INITIAL VALUE
   * =========================================================================================================
   **/

  /**
   * =========================================================================================================
   * INPUT FUNCTION
   * =========================================================================================================
   **/

  /**
   * =========================================================================================================
   * DEFINE DIALOG
   * =========================================================================================================
   **/
  /** Dialog form */
  $("#dialog-form").dialog({
    autoOpen: false,
    closeOnEscape: false,
    height: 370,
    width: 550,
    position: { my: "center", at: "top", of: $("body"), within: $("body") },
    modal: true,
    open: function () {
      // open event handler
      $(this) // the element being dialogged
        .parent() // get the dialog widget element
        .find(".ui-dialog-titlebar-close") // find the close button for this dialog
        .hide(); // hide it
    },
    buttons: {
      Save: function () {
        $("#dialog-confirm-save").html("Do you want process?");
        $("#dialog-confirm-save").dialog({
          height: 155,
          width: 400,
          modal: true,
          buttons: {
            Yes: function () {
              $(this).dialog("close");
              var edata;
              var itemCdVal = $.trim($("#itemCd-dialog").val());
              var itemCategoryVal = $.trim($("#itemCategory-dialog").val());
              var itemRevisePriceVal = $.trim(
                $("#itemRevisePrice-dialog").val()
              );
              var itemEffectiveDateFrom = $("#effectiveDateFrom-dialog").val();
              var itemEffectiveDateEnd = $("#effectiveDateEnd-dialog").val();
              var itemAttachment = $("#attachmentQuotation-dialog").val();
              var action = $.trim(
                $("#dialog-form").dialog("option", "title").substr(0, 4)
              );

              edata =
                "action=" +
                encodeURIComponent(action) +
                "&itemCdPrm=" +
                encodeURIComponent(itemCdVal) +
                "&itemCategoryPrm=" +
                encodeURIComponent(itemCategoryVal) +
                "&itemRevisePricePrm=" +
                encodeURIComponent(itemRevisePriceVal)+
                "&itemEffectiveDateFromPrm=" +
                encodeURIComponent(itemEffectiveDateFrom)+
                "&itemEffectiveDateEndPrm=" +
                encodeURIComponent(itemEffectiveDateEnd)+
                "&itemAttachmentPrm=" +
                encodeURIComponent(itemAttachment);
              $.ajax({
                type: "GET",
                url: "../db/MASTER/EPS_M_ITEM_PRICE.php",
                data: edata,
                success: function (data) {
                  $("div#dialog-mandatory-msg-1.alert").css("display", "none");
                  $("div#dialog-duplicate-msg.alert").css("display", "none");
                  $("div#dialog-notexist-msg.alert").css("display", "none");
                  $("div#dialog-notallowedit-msg.alert").css("display", "none");
                  $("div#dialog-undefined-msg.alert").css("display", "none");
                  if ($.trim(data) == "Success") {
                    $("#dialog-form").dialog("close");
                    window.location.reload();
                    $("#itemCd").val(itemCdVal);
                    $("#WMST001Form").attr("action", "WMST001.php");
                    $("#WMST001Form").submit();
                  } else if ($.trim(data) == "Mandatory_1") {
                    $("div#dialog-mandatory-msg-1.alert").css(
                      "display",
                      "block"
                    );
                    $("div#dialog-duplicate-msg.alert").css("display", "none");
                    $("div#dialog-notexist-msg.alert").css("display", "none");
                    $("div#dialog-notallowedit-msg.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-undefined-msg.alert").css("display", "none");
                  } else if ($.trim(data) == "Duplicate") {
                    $("div#dialog-mandatory-msg-1.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-duplicate-msg.alert").css("display", "block");
                    $("div#dialog-notexist-msg.alert").css("display", "none");
                    $("div#dialog-notallowedit-msg.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-undefined-msg.alert").css("display", "none");
                  } else if ($.trim(data) == "NotExist") {
                    $("div#dialog-mandatory-msg-1.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-duplicate-msg.alert").css("display", "block");
                    $("div#dialog-notexist-msg.alert").css("display", "none");
                    $("div#dialog-notallowedit-msg.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-undefined-msg.alert").css("display", "none");
                  } else if ($.trim(data) == "NotAllowEdit") {
                    $("div#dialog-mandatory-msg-1.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-duplicate-msg.alert").css("display", "none");
                    $("div#dialog-notexist-msg.alert").css("display", "none");
                    $("div#dialog-notallowedit-msg.alert").css(
                      "display",
                      "block"
                    );
                    $("div#dialog-undefined-msg.alert").css("display", "none");
                  } else if ($.trim(data) == "SessionExpired") {
                    $("#dialog-confirm-session").dialog("open");
                  } else {
                    $("div#dialog-mandatory-msg-1.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-duplicate-msg.alert").css("display", "none");
                    $("div#dialog-notexist-msg.alert").css("display", "none");
                    $("div#dialog-notallowedit-msg.alert").css(
                      "display",
                      "none"
                    );
                    $("div#dialog-undefined-msg.alert").css("display", "block");
                  }
                }
              });
            },
            No: function () {
              $(this).dialog("close");
            }
          }
        });
      },
      Cancel: function () {
        $(this).dialog("close");
      }
    }
  });

  /**
   * =========================================================================================================
   * BUTTON FUNCTION
   * =========================================================================================================
   **/
  $("#btn-search").click(function () {
    
    var itemCdVal = $.trim($("#itemCd").val());
    var itemNameVal = $.trim($("#itemName").val());
    var itemGroupCdVal = $.trim($("#itemGroupCd").val());
    var effectiveDateFromVal = $.trim($("#effectiveDateFrom").val());
    var supplierCdVal = $.trim($("#supplierCd").val());
    var itemCategoryVal = $.trim($("#itemCategory").val());
    var itemPriceVal = $.trim($("#itemPrice").val());
    var itemUpdateVal = $.trim($("#itemUpdate").val());
    var itemUpdateByVal = $.trim($("#itemUpdateBy").val());
    if (
      itemCdVal != "" ||
      itemNameVal != "" ||
      itemGroupCdVal != "" ||
      effectiveDateFromVal != "" ||
      supplierCdVal != "" ||
      itemCategoryVal != "" ||
      itemPriceVal != "" ||
      itemUpdateVal != "" ||
      itemUpdateByVal != ""
    ) {
      $("div#mandatory-msg-1.alert").css("display", "none");
      $("div#mandatory-msg-2.alert").css("display", "none");
      $("#WMST004Form").attr("action", "WMST004.php");
      $("#WMST004Form").submit();
    } else {
      $("div#mandatory-msg-1.alert").css("display", "block");
      $("div#mandatory-msg-2.alert").css("display", "none");
    }
  });

  $("#btn-reset").click(function () {
    $("#itemCd").val("");
    $("#itemName").val("");
    $("#itemGroupCd").val("");
    $("#effectiveDateFrom").val("");
    $("#supplierCd").val("");
    $("div#mandatory-msg-1.alert").css("display", "none");
    $("div#mandatory-msg-2.alert").css("display", "none");
  });

  //ambil data dari table untuk edit data
  $("table tbody tr td a.edit-data").click(function () {
    var itemCd = $.trim($(this).closest("tr").find("td:eq(1)").text());
    var itemName = $.trim($(this).closest("tr").find("td:eq(2)").text());
    var itemCategory = $.trim($(this).closest("tr").find("td:eq(3)").data('itemCategory'));
    var itemRevisePrice = $.trim($(this).closest("tr").find("td:eq(6)").text());
    var itemEffectiveDateFrom = $.trim($(this).closest("tr").find("td:eq(10)").text());
    var itemEffectiveDateEnd = $.trim($(this).closest("tr").find("td:eq(11)").text());
    var itemAttachment = $.trim($(this).closest("tr").find("td:eq(12)").data('filename'));

    $("div#dialog-mandatory-msg-1.alert").css("display", "none");
    $("div#dialog-duplicate-msg.alert").css("display", "none");
    $("div#dialog-notexist-msg.alert").css("display", "none");
    $("div#dialog-notallowedit-msg.alert").css("display", "none");
    $("div#dialog-undefined-msg.alert").css("display", "none");

    $("#dialog-form").dialog("option", "title", "Edit Item Price");

    $("#itemCd-dialog").val(itemCd);
    $("#itemName-dialog").val(itemName);
    $("#itemCategory-dialog").val(itemCategory=="Y"?"Y":"N");
    $("#itemRevisePrice-dialog").val(
      parseInt(itemRevisePrice.replace(",", ""))
    );
    $("#effectiveDateFrom-dialog").val(itemEffectiveDateFrom);
    $("#effectiveDateEnd-dialog").val(itemEffectiveDateEnd == 'Not Defined' ? "" : itemEffectiveDateEnd);
    $("#attachmentQuotation-dialog").val(itemAttachment=="-"?"":itemAttachment);

    $("#itemCd-dialog").attr("readonly", true);
    $("#itemName-dialog").attr("readonly", true);

    $("#dialog-form").dialog("open");
  });
});
