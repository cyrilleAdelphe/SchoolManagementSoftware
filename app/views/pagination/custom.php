<?php
$presenter = new Illuminate\Pagination\BootstrapPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
   
    <div class="col-sm-7">
        <div class = "pull-let">
            <?php echo $paginator->getFrom() ;?>-<?php echo $paginator->getTo() ;?>/<?php echo $paginator->getTotal() ;?>
            <div class="btn-group">
            <?php echo getPrevious($paginator->getCurrentPage(), $paginator->getUrl( $paginator->getCurrentPage()-1 ) ) ?>
            <?php echo getNext($paginator->getCurrentPage(), $paginator->getLastPage(), $paginator->getUrl( $paginator->getCurrentPage()+1 ) )  ?>
            
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
function getPrevious($currentPage, $url)
{
    if ($currentPage <= 1)
        return '<button class="btn btn-default btn-sm disabled"><i class="fa fa-chevron-right"></i></button>';
    else
       return '<a href="'.$url.'"><button class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button></a>';
}

function getNext($currentPage, $lastPage, $url)
{
    if ($currentPage >= $lastPage)
        return '<button class="btn btn-default btn-sm disabled">
                <i class="fa fa-chevron-left"></i>
              </button>';
    else
        return '<a href="'.$url.'"><button class="btn btn-default btn-sm">
                <i class="fa fa-chevron-left"></i>
              </button></a>';
}
?>