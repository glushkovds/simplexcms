var JSStructTable = function(){
    
    this.clearRights = function(){
        $('.portlet-rights label.btn').removeClass('active');
        $('.portlet-rights label.btn :checkbox').attr('checked', false);
    };
    
}, StructTable;

$(function(){
    StructTable = new JSStructTable();
});
