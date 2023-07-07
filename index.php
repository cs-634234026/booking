<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>ระบบจอง</title>
      <link href="css/style.css" rel="stylesheet" />
      <link href="css/fullcalendar.css" rel="stylesheet" />
      <link href="css/sweetalert2.css" rel="stylesheet" />
      <link href="https://fonts.googleapis.com/css?family=Noto+Sans+Thai:300,400,500,600,700" rel="stylesheet" />
      <link href="lib/bootstrap-5.2.2/css/bootstrap.min.css" rel="stylesheet" />
      <link href="lib/bootstrap-5.2.2/js/bootstrap.min.js" rel="stylesheet" />

      <script type="text/javascript" src="js/fontawesome.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
      <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

      <script src="js/moment.js"></script>
      <script src="js/sweetalert2.all.min.js"></script>

      <link href="js/fullcalendar/lib/main.css" rel="stylesheet" />
      <script src="js/fullcalendar/lib/main.js"></script>
   </head>
   <script>
      
      var changeStatusButton = document.getElementById('changeStatusButton');
      document.addEventListener('DOMContentLoaded', function () {
         var calendarEl = document.getElementById('calendar');

         var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'th',
            timeZone: 'Asia/Bangkok',
            initialView: 'dayGridMonth',
            height: 650,
            events: 'fetchEvents.php',

            schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
            selectable: true,
            select: async (start, end, allDay) => {
               let date = start.start;
               var dd = String(date.getDate()).padStart(2, '0');
               var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
               var yyyy = date.getFullYear();
               date = dd + '/' + mm + '/' + yyyy + ' - ' + dd + '/' + mm + '/' + yyyy;

               $(function () {
                  $('input[name="daterange"]').daterangepicker({
                     locale: {
                        format: 'DD/MM/YYYY',
                     },
                  });
               });

               const { value: formValues } = await Swal.fire({
                  title: 'สร้างการจอง',
                  html:
                     `<div class="row">
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">ชื่อผู้จอง</label>
                                      <input type="text" class="form-control" id="name">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">เบอร์ติดต่อ</label>
                                      <input type="text" class="form-control" maxlength="10" id="phone">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">อีเมล</label>
                                      <input type="text" class="form-control" maxlength="10" id="phone">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">วันที่เริ่ม - สิ้นสุด</label>
                                      <input type="text" class="form-control" name="daterange" id="date" value="` + date +`">
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">จำนวนสัตว์ (บาท)</label>
                                      <input type="text" class="form-control" id="deposit">
                                  </div>
                              </div>
                              <div class="col-12">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">เพิ่มเติม</label>
                                      <textarea class="form-control" rows="3" id="note"></textarea>
                                  </div>
                              </div>
                              <div class="col-6">
                                  <div class="mb-3 text-start">
                                      <label class="form-label">สี</label>
                                      <input type="color" class="form-control form-control-color" value="#212529" id="color">
                                  </div>
                              </div>
                          </div>`,
                  //  '<input id="swalEvtTitle" class="form-control" placeholder="ชื่อผู้จอง">' +
                  //  '<textarea id="swalEvtDesc" class="swal2-input" placeholder="Enter description"></textarea>' +
                  //  '<input id="swalEvtURL" class="swal2-input" placeholder="Enter URL">',
                  focusConfirm: false,
                  confirmButtonText: 'บันทึก',
                  cancelButtonText: 'ปิด',
                  showCancelButton: true,
                  cancelButtonColor: '#f8f9fa',
                  allowOutsideClick: false,
                  preConfirm: () => {
                     if (document.getElementById('name').value == '' || document.getElementById('phone').value == '') {
                        Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบ');
                     }
                     return [
                        document.getElementById('name').value,
                        document.getElementById('phone').value,
                        document.getElementById('date').value,
                        document.getElementById('deposit').value,
                        document.getElementById('note').value,
                        document.getElementById('color').value,
                     ];
                  },
               });

               if (formValues) {
                  let split_date = formValues[2].split(' - ');

                  // Add event
                  fetch('eventHandler.php', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json' },
                     body: JSON.stringify({
                        request_type: 'addEvent',
                        start: moment(split_date[0], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                        // end: moment(split_date[1], 'DD/MM/YYYY').add(1, 'days').format('YYYY-MM-DD'),
                        end: moment(split_date[1], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                        event_data: formValues,
                     }),
                  })
                     .then((response) => response.json())
                     .then((data) => {
                        console.log('sdfds', data);
                        if (data.status == 1) {
                           Swal.fire('การจองสำเร็จ!', '', 'success');
                        } else {
                           Swal.fire(data.error, '', 'error');
                        }

                        // Refetch events from all sources and rerender
                        calendar.refetchEvents();
                     })
                     .catch((error) => {
                        console.log(error);
                     });
               }
            },
            eventClick: function (info) {
               info.jsEvent.preventDefault();
               let html =
                  `<div>
                      <div class="d-flex align-item-center mb-2 text-start">
                          <span style="width: 100px;">ผู้จอง:</span>
                          <span>` +
                  info.event.extendedProps.name +
                  `</span>
                      </div>
                      <div class="d-flex align-item-center mb-2 text-start">
                          <span style="width: 100px;">เบอร์ติดต่อ:</span>
                          <span>` +
                  info.event.extendedProps.phone +
                  `</span>
                      </div>
                      <div class="d-flex align-item-center mb-2 text-start">
                          <span style="width: 100px;">จำนวน:</span>
                          <span>` +
                  (info.event.extendedProps.deposit ? info.event.extendedProps.deposit : '0') +
                  ` บาท</span>
                      </div>
                      <div class="d-flex align-item-center text-start">
                          <span style="width: 100px;">เพิ่มเติม:</span>
                          <span>` +
                  (info.event.extendedProps.note ? info.event.extendedProps.note : '-') +
                  `</span>
                      </div>
                  </div>`;
               Swal.fire({
                  icon: 'info',
                  html: html,
                  showCloseButton: true,
                  showDenyButton: true,
                  showCancelButton: true,
                  cancelButtonText: 'ปิด',
                  denyButtonText: `รออนุมัติ`,
                  confirmButtonText: 'ยกเลิกการจอง',
                  cancelButtonColor: '#f8f9fa',
                  confirmButtonColor: '#dc3545',
                  denyButtonColor: '#18E352',
                  allowOutsideClick: false,
               }).then((result) => {
                  if (result.isConfirmed) {
                     // Delete event
                     fetch('eventHandler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ request_type: 'deleteEvent', event_id: info.event.id }),
                     })
                        .then((response) => response.json())
                        .then((data) => {
                           if (data.status == 1) {
                              Swal.fire('ยกเลิกการจอง สำเร็จ!', '', 'success');
                           } else {
                              Swal.fire(data.error, '', 'error');
                           }

                           // Refetch events from all sources and rerender
                           calendar.refetchEvents();
                        })
                        .catch(console.error);
                  } else {
                     Swal.close();
                  }
               });
            },
            
            eventContent: (info) => {
               let html =
                  `<div class="p-2">
                      <div class="d-flex">
                              <i class="fa-solid fa-user pe-2"></i>
                              <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">คุณ ` +
                  info.event._def.extendedProps.name +
                  `</div>
                          </div>
                          <div class="d-flex">
                              <i class="fa-solid fa-phone pe-2"></i>
                              <div style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">` +
                  (info.event._def.extendedProps.phone == null ? '-' : info.event._def.extendedProps.phone) +
                  `</div>
                          </div>
                      </div>`;
               return { html: html };
            },
         });

         calendar.render();
      });
      
   </script>
   <body>
      <nav class="navbar bg-dark position-fixed w-100" style="z-index: 999">
         <div class="container-fluid">
            <div class="navbar-brand text-white">ระบบจอง</div>
         </div>
      </nav>
      <div class="container pt-80">
         <div id="calendar"></div>
      </div>
   </body>
</html>
