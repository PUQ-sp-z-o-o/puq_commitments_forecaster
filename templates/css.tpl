<style type = "text/css">

    /* [INPUT DATA]*/
    .input_div{
        vertical-align: top;
        width: 100%;
        display: inline-block;
    }

    .input_data{
        display: inline-block;
        vertical-align: top;
        padding: 5px 5px 5px 5px;
        width: 255px;
        border: 2px solid #dce1e7;
        margin: 1px;
       # background-color: #eeeeee;

    }

    .input_data_pdf{
        vertical-align: top;
        padding: 5px 5px 5px 5px;
        width: calc(100% - 265px);;
        display: inline-block;
        border: 2px solid #dce1e7;
    }

    .input_data label{
    }
    .input_data select{
        width: 100%;
        font-size: 14px;
        padding: 2px 2px 2px 2px;
    }

    .input_data input[type=radio]:checked ~ label{
        color: #6aec13;
    }

    .input_data input[type=text],input[type=date],input[type=file],input[type=number]{
        width: 240px;
        font-size: 14px;
        padding: 1px 1px 1px 1px;
        margin: 2px;
    }


    .input_data textarea{
        resize: none;

        width: 240px;
        font-size: 14px;
        padding: 1px 1px 1px 1px;
        margin: 2px;
    }


    .input_data select:focus,
    .input_data input:focus{
        background: #f1f1f1;
    }
    /* [AND INPUT DATA]*/

    /* [MSG] */
    .notice-success,
    .notice-info,
    .notice-error{

        animation-name: blinker;
        animation-duration: 5s;
        animation-delay: 1s;
        animation-fill-mode: forwards;
        opacity: 1;



        transform: translate(-50%, 0%);
        position: absolute;
        top: 50px;
        left: 50%;
        width:50%;
        text-align: center;
        font-size: 20px;

        z-index: 999999;
        padding: 10px;
        background-color: #22591b;
        color: white;
    }

    .notice-info{
        animation-delay: 10s;
        background-color: #36d8f4;
        color: white;
    }
    .notice-error{
        animation-delay: 120s;
        background-color: #f44336;
        color: white;
    }

    @keyframes blinker {
        from { opacity: 1.0; }
        to { opacity: 0;
            top: -100%;
        }
    }


    .notice-x{
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 2s;
    }
    /* END [MSG] */

    /* [buttons]*/
    .button {
     border: none;
     color: white;
     padding: 4px 5px;
     text-align: center;
     text-decoration: none;
     display: inline-block;
     font-size: 13px;
     margin: 4px 2px;
     transition-duration: 1.5s;
     cursor: pointer;
}

.green {
     background-color: #4CAF50;
     color: white;
     #border: 4px solid #4CAF50;
}
.green:hover {
     background-color: #4a8b4d;
     color: white;
}
.blue {
     background-color: #1a4d80;
     color: white;
    # border: 4px solid #008CBA;
}
.blue:hover {
     background-color: #008CBA;
     color: white;
}
.red {
     background-color: #f44336;
     color: white;
     #border: 4px solid #f44336;
}
.red:hover {
     background-color: #f77d74;
     color: white;
}
.gray {
     background-color: #e7e7e7;
     color: black;
     #border: 4px solid #e7e7e7;
}
.gray:hover {
     background-color: #959595;
}
.black {
     background-color: white;
     color: black;
     border: 4px solid #555555;
}
.black:hover {
     background-color: #555555;
     color: white;
}

/*
INPUTS
 */

.input{
     width: 100%;
     font-size: 13px;
     padding: 0px 4px 0px 4px;
}
.input:focus{
     background: #f1f1f1;
}
.px300{
     width: 300px;
}

/*
TABLESS
 */
.table_list {
     border-collapse: collapse;
     width: 100%;
     border: 1px solid #ddd;
     font-size: 12px;
}
.table_list th, #table_list td {

}
.table_list tr {
     border-bottom: 1px solid #ddd;
}
.table_list tr.header,
.table_list tr:hover {
     background-color: #f1f1f1;

}

/*
TABS
 */
    .Ptabs {
        position: relative;
        vertical-align: top;
        width: calc(100% - 265px);
        display: inline-block;
    }
    .Ptab, .Ptab-title {
        display: inline-block;
    }
    .Ptab input[type="radio"] { display: none; }
    .Ptab-title {
        position: relative;
        background: #dce1e7;
        padding: 5px 5px;
        border: 2px solid #dce1e7;
        border-bottom: none;
        top: 7px;
    }
    .Ptab-content {
        overflow-y: scroll;
        border: 2px solid #dce1e7;
        position: absolute;
        padding: 5px;
        left: 0;
        width: 100%;
        display: none;
    }
    .Ptab :checked + .Ptab-title {
        background: #fff;
        border: 2px solid #dce1e7;
        padding: 5px 5px 5px 5px;
        border-bottom: none;
        top: 7px;
        z-index: 1;
    }
    .Ptab :checked ~ .Ptab-content {
        display: block;
        border: 2px solid #dce1e7;
    }


/*
TAGS
 */
    .tags-input-wrapper{
        background: transparent;
        padding: 5px;
        border-radius: 2px;
        border: 1px solid rgba(0, 0, 0, 0.77)
    }

    .tags-input-wrapper input{
        border: none;
        background: transparent;
        outline: none;
        margin-right: 15px;
        width: 240px;
        font-size: 14px;
        padding: 10px 10px 10px 10px;
    }
    .tags-input-wrapper input:focus{
        background: transparent;
        outline: none;
        display: block;
    }
    .tags-input-wrapper .tag{
        display: inline-block;
        background-color: #dce1e7;
        color: #000000;
        border-radius: 40px;
        padding: 0px 3px 0px 7px;
        margin-right: 5px;
        margin-bottom:5px;

    }
    .tags-input-wrapper .tag a {
        margin: 0 7px 3px;
        display: inline-block;
        cursor: pointer;
    }

    .tags-list{
        background-color: #728093;
        color: #ffffff;
        font-style: italic;
        border-radius: 5px;
        padding: 0px 5px 1px 2px;
    }
</style>

